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

use App\Entity\Order;
use App\Entity\User;
use App\Exception\NoOrderException;
use App\Form\CreditFormType;
use App\Form\Model\ChangePassword;
use App\Form\Model\CreditOrder;
use App\Form\PasswordFormType;
use App\Form\ProfileFormType;
use App\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\PluginController\Exception\InvalidPaymentInstructionException;
use JMS\Payment\CoreBundle\PluginController\PluginController;
use JMS\Payment\CoreBundle\PluginController\Result;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Customer controller.
 *
 * @Route("/customer")
 *
 * @Security("is_granted('ROLE_USER')")
 */
class CustomerController extends AbstractController
{
    /**
     * Edit profile.
     *
     * @Route("/profile", name="customer_profile")
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
     * Edit profile.
     *
     * @Route("/password", name="customer_password")
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
     * Select credit.
     *
     * @Route("/select-credit", name="customer_credit")
     *
     * @param Request      $request      Request handling data
     * @param OrderManager $orderManager Command manager
     *
     * @return Response|RedirectResponse
     */
    public function selectCredit(Request $request, OrderManager $orderManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $order = $orderManager->getOrCreateCartedOrder($user);
        $model = new CreditOrder();
        $model->initializeWithOrder($order);
        $form = $this->createForm(CreditFormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderManager->pushOrderedArticles($order, $model);
            $orderManager->save($order);
            $this->addFlash('success', 'flash.order.step1');

            return $this->redirectToRoute('customer_buy');
        }

        return $this->render('customer/select-credit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * Select credit.
     *
     * @Route("/buy-credit", name="customer_buy")
     *
     * @param Request          $request      Request for form
     * @param OrderManager     $orderManager Command manager
     * @param PluginController $ppc          Plugin controller
     *
     * @throws InvalidPaymentInstructionException when choosing an invalid method
     *
     * @return Response|RedirectResponse
     */
    public function buyCredit(Request $request, OrderManager $orderManager, PluginController $ppc): Response
    {
        $user = $this->getUser();

        //find carted (non canceled and non paid) and non empty order
        try {
            $order = $orderManager->getNonEmptyCartedOrder($user);
        } catch (NoOrderException $e) {
            //there is no order which is not empty, not canceled and no paid
            $this->addFlash('warning', 'flash.order.no-step1');
            return $this->redirectToRoute('customer_credit');
        }

        $form = $this->createForm(ChoosePaymentMethodType::class, null, [
            'amount' => (float) $order->getPrice() + (float) $order->getVat(),
            'currency' => 'EUR',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //We know how customer want to pay
            $ppc->createPaymentInstruction($instruction = $form->getData());
            $order->setPaymentInstruction($instruction);
            $orderManager->save($order);

            return $this->redirectToRoute('customer_payment_create');
        }

        return $this->render('customer/buy-credit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * Payment create.
     *
     * @Route("/payment-create", name="customer_payment_create")
     *
     * @param OrderManager     $orderManager order manager to retrieve order
     * @param PluginController $ppc          plugin controller
     *
     * @throws InvalidPaymentInstructionException when error occurred
     *
     * @return RedirectResponse
     */
    public function paymentCreateAction(OrderManager $orderManager, PluginController $ppc)
    {
        //find order
        try {
            $user = $this->getUser();
            //FIXME Add a test on payment is not null!
            $order = $orderManager->getNonEmptyCartedOrder($user);
        } catch (Exception $e) {
            //there is no order which is not empty, not canceled and no paid
            return $this->redirectToRoute('customer_credit');
        }

        $payment = $this->createPayment($order, $ppc);
        $result = $ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());

        if (Result::STATUS_SUCCESS === $result->getStatus()) {
            $orderManager->setOrderPaid($order);
            $orderManager->save($order);

            return $this->redirectToRoute('customer_payment_complete');
        }

        //For PAYPAL-like process (payment on another site)
        if (Result::STATUS_PENDING === $result->getStatus()) {
            $exception = $result->getPluginException();

            if ($exception instanceof ActionRequiredException) {
                $action = $exception->getAction();

                if ($action instanceof VisitUrl) {
                    return $this->redirect($action->getUrl());
                }
            }
        }

        // In a real-world application you wouldn't throw the exception. You would,
        // for example, redirect to the showAction with a flash message informing
        // the user that the payment was not successful.
        // FIXME do it
        // log it
        throw $result->getPluginException();
    }

    /**
     * Payment completed!
     *
     * @Route("/payment-complete", name="customer_payment_complete")
     */
    public function paymentCompleteAction()
    {
        //FIXME TADA !
        return new Response('Payment complete');
    }

    /**
     * Create payment.
     *
     * @param Order            $order order
     * @param PluginController $ppc   plugin controller
     *
     * @throws InvalidPaymentInstructionException when error occurred
     *
     * @return PaymentInterface
     */
    private function createPayment(Order $order, PluginController $ppc): PaymentInterface
    {
        $instruction = $order->getPaymentInstruction();
        $pendingTransaction = $instruction->getPendingTransaction();

        if (null !== $pendingTransaction) {
            return $pendingTransaction->getPayment();
        }

        $amount = $instruction->getAmount() - $instruction->getDepositedAmount();

        return $ppc->createPayment($instruction->getId(), $amount);
    }
}
