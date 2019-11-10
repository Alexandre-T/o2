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

use App\Entity\File;
use App\Entity\Programmation;
use App\Entity\User;
use App\Exception\NoArticleException;
use App\Exception\SettingsException;
use App\Form\ChoosePaymentMethodType;
use App\Form\CreditFormType;
use App\Form\Model\ChangePassword;
use App\Form\Model\CreditOrder;
use App\Form\Model\Programmation as ProgrammationModel;
use App\Form\Model\Vat;
use App\Form\PasswordFormType;
use App\Form\ProfileFormType;
use App\Form\ProgrammationFormType;
use App\Form\VatFormType;
use App\Mailer\MailerInterface;
use App\Manager\ArticleManager;
use App\Manager\AskedVatManager;
use App\Manager\OrderManager;
use App\Manager\ProgrammationManager;
use App\Manager\SettingsManager;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Customer controller.
 *
 * @Route("/customer", name="customer_")
 *
 * @Security("is_granted('ROLE_USER')")
 */
class CustomerController extends AbstractController
{
    /**
     * Customer order a new programmation.
     *
     * @Route("/programmation/new", name="file_new")
     *
     * @param LoggerInterface      $logger               logger to alert when settings are missing
     * @param MailerInterface      $mailer               mailer interface to send mail to programmer
     * @param Request              $request              request handling data
     * @param ProgrammationManager $programmationManager programmation manger to save new programmation
     * @param SettingsManager      $settingsManager      settings manager
     * @param UserManager          $userManager          to update credit of user
     *
     * @return Response
     */
    public function newProgrammation(
     LoggerInterface $logger,
     MailerInterface $mailer,
     Request $request,
     ProgrammationManager $programmationManager,
     SettingsManager $settingsManager,
     UserManager $userManager
    ): Response {
        $model = new ProgrammationModel();
        $model->setCustomerCredit($this->getUser()->getCredit());
        $form = $this->createForm(ProgrammationFormType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $programmation = new Programmation();
            $file = new File();
            $model->copyProgrammation($programmation);
            $model->copyFile($file);
            $programmation->setCustomer($user);
            $programmation->setOriginalFile($file);
            $userManager->debit($programmation);
            $programmationManager->save($file);
            $programmationManager->save($programmation);
            try {
                /** @var string $programmer */
                $programmer = $settingsManager->getValue('mail-programmer');
                /** @var string $sender */
                $sender = $settingsManager->getValue('mail-sender');
                $mailer->sendProgrammationMail($programmation, $programmer, $sender);
            } catch (SettingsException $exception) {
                $logger->alert('Mail not sent to programmer:'.$exception->getMessage());
            }

            $userManager->save($user);
            $this->addFlash('success', 'flash.programmation-purchased');

            return $this->redirectToRoute('customer_programmation_show', [
                'id' => $programmation->getId(),
            ]);
        }

        return $this->render('customer/file/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Step1: Customer select items.
     *
     * @Route("/order-credit", name="order_credit")
     *
     * @param Request      $request      Request handling data
     * @param OrderManager $orderManager Command manager
     *
     * @return Response|RedirectResponse
     */
    public function orderCredit(Request $request, OrderManager $orderManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $orderManager->getOrCreateCartedOrder($user);
        $model = new CreditOrder();
        $model->init($order);
        $form = $this->createForm(CreditFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderManager->pushOrderedArticles($order, $model);
            $orderManager->save($order);
            $this->addFlash('success', 'flash.order.step1');

            return $this->redirectToRoute('customer_payment_method');
        }

        return $this->render('customer/order-credit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * Step1: Customer orders cmd.
     *
     * @Route("/order-cmd", name="order_cmd")
     *
     * @param Request        $request        Request handling data
     * @param OrderManager   $orderManager   Command manager
     * @param ArticleManager $articleManager Article manager
     *
     * @return Response|RedirectResponse
     *
     * @throws NoArticleException when cmdslave article does not exists
     */
    public function orderCmd(Request $request, OrderManager $orderManager, ArticleManager $articleManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $orderManager->retrieveOrCreateCmdOrder($user);
        $form = $this->createForm(ChoosePaymentMethodType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderManager->save($order);
            dd('ok');
        }

        $article = $articleManager->retrieveByCode('cmdslave');

        return $this->render('customer/order-cmd.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'article' => $article,
        ]);
    }

    /**
     * Edit profile.
     *
     * @Route("/password", name="password")
     *
     * @param Request                $request       the request handling data
     * @param EntityManagerInterface $entityManager entity manager to save user
     *
     * @return Response|RedirectResponse
     */
    public function password(Request $request, EntityManagerInterface $entityManager): Response
    {
        $model = new ChangePassword();
        $form = $this->createForm(PasswordFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $user->setPlainPassword($model->getNewPassword());
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'flash.password.updated');

            return $this->redirectToRoute('home');
        }

        if ($form->isSubmitted()) {
            $this->addFlash('error', 'flash.password.not-updated');
        }

        return $this->render('customer/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit profile.
     *
     * @Route("/profile", name="profile")
     *
     * @param Request                $request       the request handling data
     * @param EntityManagerInterface $entityManager entity manager to save user
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfileFormType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'flash.profile.updated');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'flash.profile.not-updated');
        }

        return $this->render('customer/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Show and update current VAT profile.
     *
     * @Route("/vat", name="vat")
     *
     * @param Request         $request         the request to handle data form
     * @param AskedVatManager $askedVatManager the manager to save data
     * @param MailerInterface $mailer          the mailer to send a mail
     *
     * @return Response
     */
    public function updateVat(Request $request, AskedVatManager $askedVatManager, MailerInterface $mailer): Response
    {
        $customer = $this->getUser();
        $model = new Vat();
        $model->setVat($customer->getVat());
        $model->setExplanation($customer->getBillIndication());
        $model->setActual($customer->getVat());
        $form = $this->createForm(VatFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Vat $data */
            $data = $form->getData();
            $asked = $askedVatManager->askVat($customer, $data->getVat(), $data->getExplanation());

            $this->addFlash('success', 'flash.asked-vat.sent');

            $mailer->sendAskedVat($asked);

            return $this->redirectToRoute('home');
        }

        return $this->render('customer/vat.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
