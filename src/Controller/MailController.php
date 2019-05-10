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

namespace App\Controller;

use App\Exception\SettingsException;
use App\Form\MailFormType;
use App\Mailer\MailerInterface;
use App\Manager\SettingsManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * MailController class.
 *
 * @Route("administration/mail", name="administration_mail_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class MailController extends AbstractPaginateController
{
    /**
     * Limit of mail per page for listing.
     */
    public const LIMIT_PER_PAGE = 25;

    /**
     * Displays a form to send a test mail.
     *
     * @Route("/", name="main", methods={"get", "post"})
     *
     * @param MailerInterface $mailer          the mailer interface to send mail
     * @param Request         $request         The request to handle method
     * @param SettingsManager $settingsManager The Setting manager to retrieve main mail
     *
     * @return RedirectResponse|Response
     */
    public function edit(
     MailerInterface $mailer,
     Request $request,
     SettingsManager $settingsManager
    ): Response {
        try {
            /** @var string $email */
            $email = $settingsManager->getValue('mail-sender');
        } catch (SettingsException $e) {
            $this->addFlash('error', 'flash.mail-test.sender-not-declared');

            return $this->redirectToRoute('home');
        }

        $mailForm = $this->createForm(MailFormType::class);
        $mailForm->handleRequest($request);
        if ($mailForm->isSubmitted() && $mailForm->isValid()) {
            $message = 'flash.mail-test.sent';
            $number = $mailer->sendTestMail($email);
            $type = 'success';

            if (empty($number)) {
                $message = 'flash.mail-test.not-sent';
                $type = 'error';
            }

            $this->addFlash($type, $message);

            return $this->redirectToRoute('administration_mail_main');
        }

        return $this->render('administration/mail/main.html.twig', [
            'form' => $mailForm->createView(),
            'email' => $email,
        ]);
    }
}
