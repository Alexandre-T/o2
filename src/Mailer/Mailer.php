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

use App\Entity\Bill;
use App\Entity\Order;
use App\Entity\Programmation;
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
     * Send a mail to accountant from sender to inform about the new order and the new bill.
     *
     * @param Order  $order      the new order
     * @param Bill   $bill       the new bill
     * @param string $sender     the sender of mail
     * @param string $accountant the accountant who received mail
     *
     * @return int
     */
    public function sendPaymentMail(Order $order, Bill $bill, string $sender, string $accountant): int
    {
        $downloadBill = $this->router->generate(
            'accountant_bill_show',
            ['id' => $bill->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $parameters = [
            'downloadBill' => $downloadBill,
            'mail' => $order->getCustomer()->getMail(),
            'amount' => $bill->getAmount(),
            'credits' => $order->getCredits(),
        ];

        $renderHtml = $this->templating->render('mail/new-payment.html.twig', $parameters);
        $renderTxt = $this->templating->render('mail/new-payment.txt.twig', $parameters);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $sender, $accountant);
    }

    /**
     * Send an email to programmer to inform that a new programmation was ordered.
     *
     * @param Programmation $programmation the new programmation
     * @param string        $programmer    the mail programmer
     * @param string        $sender        the expediter
     *
     * @return int
     */
    public function sendProgrammationMail(Programmation $programmation, string $programmer, string $sender): int
    {
        $download = $this->router->generate(
            'programmer_download_original',
            ['id' => $programmation->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $parameters = [
            'download' => $download,
            'mail' => $programmation->getCustomer()->getMail(),
            'programmation' => $programmation,
        ];

        $renderHtml = $this->templating->render('mail/new-programmation.html.twig', $parameters);
        $renderTxt = $this->templating->render('mail/new-programmation.txt.twig', $parameters);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $sender, $programmer);
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
     * Sent a mail to alert customer that his programmation is done.
     *
     * @param Programmation $programmation programmation done
     * @param string        $sender        expediter
     *
     * @return int
     */
    public function sendReturningProgrammation(Programmation $programmation, string $sender): int
    {
        $download = $this->router->generate(
            'customer_programmation_download',
            ['id' => $programmation->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $show = $this->router->generate(
            'customer_programmation_show',
            ['id' => $programmation->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = $programmation->getCustomer()->getMail();

        $parameters = [
            'download' => $download,
            'show' => $show,
            'mail' => $email,
            'programmation' => $programmation,
        ];

        $renderHtml = $this->templating->render('mail/programmation-done.html.twig', $parameters);
        $renderTxt = $this->templating->render('mail/programmation-done.txt.twig', $parameters);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $sender, $email);
    }

    /**
     * Send an internal test email to declared user in settings.
     *
     * @param string $email mail of senders and receivers
     *
     * @return int
     */
    public function sendTestMail(string $email): int
    {
        $subject = 'Test Mail | Mail de test';
        $txt = 'This is a test mail. Ceci est un mail de test';
        $html = '<p>This is a <strong>test</strong> mail. Ceci est un mail de <strong>test</strong>.</p>';

        $message = ($this->mailer->createMessage())
            ->setSubject($subject)
            ->setFrom($email)
            ->setTo($email)
            ->setBody($html, 'text/html')
            ->addPart($txt, 'text/plain')
        ;

        return $this->mailer->send($message);
    }

    /**
     * Send a mail.
     *
     * @param string       $html      the mail body in html
     * @param string       $txt       the mail body in txt
     * @param string       $fromEmail mail expediter
     * @param array|string $toEmail   mail recipient
     *
     * @return int the number of sent mail
     */
    protected function sendEmailMessage(string $html, string $txt, string $fromEmail, $toEmail): int
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

        return $this->mailer->send($message);
    }
}
