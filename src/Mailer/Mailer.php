<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\User;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Mailer implements MailerInterface
{
    /**
     * Swift mailer.
     *
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * Url generator.
     *
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * Twig engine.
     *
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Parameters.
     *
     * @var array sender, etc
     */
    private $parameters;

    /**
     * Mailer constructor.
     *
     * @param Swift_Mailer          $mailer     mailer
     * @param UrlGeneratorInterface $router     the url generator
     * @param EngineInterface       $templating the templating engine
     * @param array                 $parameters the env parameters (expediter)
     */
    public function __construct(
     Swift_Mailer $mailer,
     UrlGeneratorInterface  $router,
     EngineInterface $templating,
     array $parameters
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->parameters = $parameters['parameters'];
    }

    /**
     * Send a mail to reset password.
     *
     * @param User $user recipient mail
     */
    public function sendResettingEmailMessage(User $user): void
    {
        $url = $this->router->generate(
            'security_reset',
            ['token' => $user->getResettingToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $renderHtml = $this->templating->render('mail/resetting.html.twig', [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $renderTxt = $this->templating->render('mail/resetting.txt.twig', [
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        $this->sendEmailMessage($renderHtml, $renderTxt, $this->parameters['from'], $user->getMail());
    }

    /**
     * Send a mail.
     *
     * @param string       $html      the mail body in html
     * @param string       $txt       the mail body in txt
     * @param string       $fromEmail mail expediter
     * @param array|string $toEmail   mail recipient
     */
    protected function sendEmailMessage(string $html, string $txt, string $fromEmail, $toEmail): void
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($txt));
        $subject = array_shift($renderedLines);
        $txt = implode("\n", $renderedLines);
        $message = ($this->mailer->createMessage())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($html, 'text/html')
            ->addPart($txt, 'text/plain')
        ;
        $this->mailer->send($message);
    }
}
