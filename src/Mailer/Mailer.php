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

use App\Entity\AskedVat;
use App\Entity\Bill;
use App\Entity\Order;
use App\Entity\Programmation;
use App\Entity\User;
use App\Exception\SettingsException;
use App\Manager\SettingsManager;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Mailer.
 */
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
     * @var Environment
     */
    protected $twig;

    /**
     * The logger interface.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Default settings from setting manager.
     *
     * @var SettingsManager
     */
    private $settingsManager;

    /**
     * Mailer constructor.
     *
     * @param LoggerInterface $logger          logger service
     * @param Swift_Mailer    $mailer          mailer service
     * @param Environment     $twig            the twig templating engine replacing php templating
     * @param SettingsManager $settingsManager the settings manager to retrieve settings
     */
    public function __construct(
     LoggerInterface $logger,
     Swift_Mailer $mailer,
     Environment $twig,
     SettingsManager $settingsManager
    ) {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->settingsManager = $settingsManager;
    }

    /**
     * Send a mail to accountant to alert him that a customer is asking for a new VAT.
     *
     * @param AskedVat $asked the asked vat
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     *
     * @return int the number of mails sent (shall be 1)
     */
    public function sendAskedVat(AskedVat $asked): int
    {
        try {
            $from = $this->getDefaultSender();
            $to = $this->getAccountantMail();
        } catch (SettingsException $e) {
            $this->logSettingsException($e);

            return 0;
        }

        $html = $this->getHtmlAskedVat($asked);
        $txt = $this->getTxtAskedVat($asked);

        return $this->sendEmailMessage($html, $txt, $from, $to);
    }

    /**
     * Send a mail to customer to inform him that accountant accepted his new VAT rate.
     *
     * @param AskedVat $asked the asked vat entity
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    public function sendAskedVatAccepted(AskedVat $asked): int
    {
        try {
            $from = $this->getAccountantMail();
        } catch (SettingsException $e) {
            $this->logSettingsException($e);

            return 0;
        }

        $html = $this->getHtmlAskedVatAccepted($asked);
        $txt = $this->getTxtAskedVatAccepted($asked);
        $to = $asked->getCustomer()->getMail();

        return $this->sendEmailMessage($html, $txt, $from, $to);
    }

    /**
     * Send a mail to customer to inform him that accountant rejected his new VAT rate.
     *
     * @param AskedVat $asked the asked vat entity
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    public function sendAskedVatRejected(AskedVat $asked): int
    {
        try {
            $from = $this->getAccountantMail();
        } catch (SettingsException $e) {
            $this->logSettingsException($e);

            return 0;
        }

        $html = $this->getHtmlAskedVatRejected($asked);
        $txt = $this->getTxtAskedVatRejected($asked);
        $to = $asked->getCustomer()->getMail();

        return $this->sendEmailMessage($html, $txt, $from, $to);
    }

    /**
     * Send a mail to accountant to inform him that a user is registering to olsx program.
     *
     * @param User $user the subscriber
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     *
     * @return int number of mail sent
     */
    public function sendOlsxRegistering(User $user): int
    {
        try {
            $from = $this->getDefaultSender();
            $to = $this->getAccountantMail();
        } catch (SettingsException $e) {
            $this->logSettingsException($e);

            return 0;
        }

        $html = $this->getHtmlOlsxRegistering($user);
        $txt = $this->getTxtOlsxRegistering($user);

        return $this->sendEmailMessage($html, $txt, $from, $to);
    }

    /**
     * Send a mail to accountant from sender to inform about the new order and the new bill.
     *
     * @param Order  $order      the new order
     * @param Bill   $bill       the new bill
     * @param string $sender     the sender of mail
     * @param string $accountant the accountant who received mail
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    public function sendPaymentMail(Order $order, Bill $bill, string $sender, string $accountant): int
    {
        $parameters = [
            'id' => $bill->getId(),
            'mail' => $order->getCustomer()->getMail(),
            'amount' => $bill->getAmount(),
            'credits' => $order->getCredits(),
        ];

        $renderHtml = $this->twig->render('mail/new-payment.html.twig', $parameters);
        $renderTxt = $this->twig->render('mail/new-payment.txt.twig', $parameters);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $sender, $accountant);
    }

    /**
     * Send an email to programmer to inform that a new programmation was ordered.
     *
     * @param Programmation $programmation the new programmation
     * @param string        $programmer    the mail programmer
     * @param string        $sender        the expediter
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    public function sendProgrammationMail(Programmation $programmation, string $programmer, string $sender): int
    {
        $parameters = [
            'id' => $programmation->getId(),
            'mail' => $programmation->getCustomer()->getMail(),
            'programmation' => $programmation,
        ];

        $renderHtml = $this->twig->render('mail/new-programmation.html.twig', $parameters);
        $renderTxt = $this->twig->render('mail/new-programmation.txt.twig', $parameters);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $sender, $programmer);
    }

    /**
     * Send a mail to reset password.
     *
     * @param User $user recipient mail
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    public function sendResettingEmailMessage(User $user): void
    {
        $renderHtml = $this->twig->render('mail/resetting.html.twig', [
            'token' => $user->getResettingToken(),
        ]);
        $renderTxt = $this->twig->render('mail/resetting.txt.twig', [
            'token' => $user->getResettingToken(),
        ]);

        try {
            $this->sendEmailMessage($renderHtml, $renderTxt, $this->getDefaultSender(), $user->getMail());
        } catch (SettingsException $exception) {
            $this->logger->warning('Unable to send mail, because of settings exception:'.$exception->getMessage());
        }
    }

    /**
     * Sent a mail to alert customer that his programmation is done.
     *
     * @param Programmation $programmation programmation done
     * @param string        $sender        expediter
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    public function sendReturningProgrammation(Programmation $programmation, string $sender): int
    {
        $email = $programmation->getCustomer()->getMail();

        $parameters = [
            'mail' => $email,
            'programmation' => $programmation,
        ];

        $renderHtml = $this->twig->render('mail/programmation-done.html.twig', $parameters);
        $renderTxt = $this->twig->render('mail/programmation-done.txt.twig', $parameters);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $sender, $email);
    }

    /**
     * Send an internal test email to declared user in settings.
     *
     * @param string $email mail of senders and receivers
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

    /**
     * Return the accountant mail.
     *
     * @throws SettingsException if mail-accountant does not exists
     */
    private function getAccountantMail(): string
    {
        return (string) $this->settingsManager->getValue('mail-accountant');
    }

    /**
     * Return the default sender.
     *
     * @throws SettingsException if mail-sender does not exists
     */
    private function getDefaultSender(): string
    {
        return (string) $this->settingsManager->getValue('mail-sender');
    }

    /**
     * Get the html content when accountant is alerted that a customer is asking a new vat rate.
     *
     * @param AskedVat $asked the entity recorded
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    private function getHtmlAskedVat(AskedVat $asked): string
    {
        return $this->twig->render('mail/new-asked-vat.html.twig', [
            'asked' => $asked,
        ]);
    }

    /**
     * Get the html content when accountant accepted a new vat rate for a customer.
     *
     * @param AskedVat $asked the entity recorded
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    private function getHtmlAskedVatAccepted(AskedVat $asked): string
    {
        return $this->twig->render('mail/accepted-asked-vat.html.twig', [
            'asked' => $asked,
        ]);
    }

    /**
     * Get the html content when accountant accepted a new vat rate for a customer.
     *
     * @param AskedVat $asked the entity recorded
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    private function getHtmlAskedVatRejected(AskedVat $asked): string
    {
        return $this->twig->render('mail/rejected-asked-vat.html.twig', [
            'asked' => $asked,
        ]);
    }

    /**
     * Get the mail content when user is registering to the olsx program.
     *
     * @param User $user the subscriber
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     *
     * @return string the html text
     */
    private function getHtmlOlsxRegistering(User $user): string
    {
        return $this->twig->render('mail/olsx-registering.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Get the html content when accountant is alerted that a customer is asking a new vat rate.
     *
     * @param AskedVat $asked the entity recorded
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    private function getTxtAskedVat(AskedVat $asked): string
    {
        return $this->twig->render('mail/new-asked-vat.txt.twig', [
            'asked' => $asked,
        ]);
    }

    /**
     * Get the txt content when accountant accepted a new vat rate for a customer.
     *
     * @param AskedVat $asked the entity recorded
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    private function getTxtAskedVatAccepted(AskedVat $asked): string
    {
        return $this->twig->render('mail/accepted-asked-vat.txt.twig', [
            'asked' => $asked,
        ]);
    }

    /**
     * Get the txt content when accountant accepted a new vat rate for a customer.
     *
     * @param AskedVat $asked the entity recorded
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     */
    private function getTxtAskedVatRejected(AskedVat $asked): string
    {
        return $this->twig->render('mail/rejected-asked-vat.txt.twig', [
            'asked' => $asked,
        ]);
    }

    /**
     * Get the text mail content when user is registering to the olsx program.
     *
     * @param User $user the subscriber
     *
     * @throws LoaderError  on load error
     * @throws RuntimeError on runtime error
     * @throws SyntaxError  on syntax error
     *
     * @return string the html text
     */
    private function getTxtOlsxRegistering(User $user): string
    {
        return $this->twig->render('mail/olsx-registering.txt.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Log any settings exception.
     *
     * @param SettingsException $exception the exception throws when asking a non-existent setting
     *
     * @return int Zero because no mail was sent
     */
    private function logSettingsException(SettingsException $exception): int
    {
        $this->logger->warning('Unable to send mail, because of settings exception:'.$exception->getMessage());

        return 0;
    }
}
