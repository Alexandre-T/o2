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

use App\Entity\Bill;
use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\User;
use App\Exception\NoOrderException;
use App\Exception\SettingsException;
use App\Form\ChoosePaymentMethodType;
use App\Form\Model\PaymentMethod;
use App\Mailer\MailerInterface;
use App\Manager\BillManager;
use App\Manager\OrderManager;
use App\Manager\PaymentManager;
use App\Manager\SettingsManager;
use App\Model\OrderInterface;
use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Security\TokenInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * Payment controller .
 *
 * @Route("/payment")
 */
class PaymentController extends AbstractController
{
    /**
     * Step3: When using Paypal, i got the good status.
     * I analyse status to redirect user to the cancel page or to validate payment.
     *
     * @Route("/done", name="customer_payment_done")
     *
     * @param Request         $request         Request for form
     * @param BillManager     $billManager     bill manager
     * @param OrderManager    $orderManager    order manager
     * @param SettingsManager $settingsManager setting manager
     * @param LoggerInterface $logger          the logger to log
     * @param MailerInterface $mailer          the mailer interface to send command completed
     * @param Payum           $payum           Payum manager
     * @param LoggerInterface $log             the logger
     *
     * @return redirectResponse|Response
     *
     * NO SECURITY because user can logout just before payment
     */
    public function done(
        Request $request,
        BillManager $billManager,
        OrderManager $orderManager,
        SettingsManager $settingsManager,
        LoggerInterface $logger,
        MailerInterface $mailer,
        Payum $payum,
        LoggerInterface $log
    ): Response {
        try {
            $token = $payum->getHttpRequestVerifier()->verify($request);
        } catch (Throwable $e) {
            //I do not have the token.
            $this->addFlash('error', 'error.payment.non-existent');
            $log->warning('This order does not exists or has been already paid. Token does not exist.');

            return $this->redirectToRoute('home');
        }

        $gatewayName = $token->getGatewayName();
        $log->info(sprintf('Payment done page with gateway %s', $gatewayName));
        $gateway = $payum->getGateway($gatewayName);

        // you can invalidate the token. The url could not be requested any more.
        // $payum->getHttpRequestVerifier()->invalidate($token);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        /** @var Payment $payment */
        $payment = $status->getFirstModel();
        $order = $payment->getOrder();

        if ($order->isPaid()) {
            $level = LogLevel::WARNING;
            $message = sprintf('Order already paid and credited for Order %d', $order->getId());
            if (!$order->isCredited()) {
                $orderManager->credit($order);
                $logger->warn('This order was paid but not credited. Order credited');
            }

            if ('monetico' === $gatewayName) {
                $level = LogLevel::INFO;
                $message = sprintf('Order %d already paid because of monetico notification', $order->getId());
            }

            $bill = $billManager->getLastBill($order);
            if ($bill instanceof Bill) {
                $logger->log($level, $message);

                return $this->redirectToRoute('customer_bill_show', ['id' => $bill->getId()]);
            }

            //No bill !
            $logger->warning(sprintf(
                'No bill for order %d of customer %s',
                $order->getId(),
                $order->getCustomer()->getLabel()
            ));

            return $this->redirectToRoute('customer_bill_list');
        }

        if ($status->isAuthorized() || $status->isCaptured()) {
            $orderManager->validateAfterPaymentComplete($order);
            $bill = $billManager->retrieveOrCreateBill($order, $order->getCustomer());
            $orderManager->save($order);
            $billManager->save($bill);
            if ($token instanceof TokenInterface) {
                $payum->getHttpRequestVerifier()->invalidate($token);
            }

            //Mail sending
            $this->prepareAndSendMail($logger, $mailer, $settingsManager, $order, $bill);

            return $this->render('payment/complete.html.twig', [
                'paymentSystemName' => $gatewayName,
                'order' => $order,
            ]);
        }

        if ($status->isPending() || $status->isNew() && 'monetico' === $gatewayName) {
            //Paypal Sandbox : Review is ON.
            //Paypal : Review is ON
            //Monetico : Notification was not received. Customer was faster than Monetico
            $orderManager->setPending($order);
            $orderManager->save($order);
            $logger->info(sprintf(
                'Order %d of customer "%s" is now pending. Payment via %s',
                $order->getId(),
                $order->getCustomer()->getLabel(),
                $gatewayName
            ));

            $this->addFlash('success', sprintf(
                'flash.order.pending.%s',
                $gatewayName
            ));

            return $this->redirectToRoute('customer_orders_pending');
        }

        if ($order->isPending() && ($status->isCanceled() || $status->isFailed())) {
            $orderManager->setCancel($order);
            $orderManager->save($order);
            $logger->info(sprintf(
                'Converting order %d of customer "%s" from PENDING to CANCELED because of status "%s"',
                $order->getId(),
                $order->getCustomer()->getLabel(),
                $status->getValue()
            ));

            return $this->redirectToRoute('home');
        }

        //Another status. We considere this is canceled
        $this->addFlash('warning', 'error.payment.canceled');
        $route = $this->getPaymentMethodRoute($order->getNature());

        $log->info(sprintf(
            'Payment %d for order %d of customer "%s" canceled (payum status: "%s")',
            $payment->getId(),
            $order->getId(),
            $order->getCustomer()->getLabel(),
            $status->getValue()
        ));

        return $this->redirectToRoute($route);
    }

    /**
     * Payment has been canceled by user.
     * He didn't ask to recover money, he only click on cancel during payment.
     *
     * @Route("/cancel/{order}", name="customer_payment_cancel")
     *
     * @param Order           $order $order order which payment was not completed
     * @param LoggerInterface $log   log canceled payment
     *
     * @return RedirectResponse
     *
     * @Security("is_granted('ROLE_USER')")
     */
    public function paymentCancel(Order $order, LoggerInterface $log)
    {
        $this->addFlash('warning', 'error.payment.canceled');
        $log->log(LogLevel::INFO, 'Payment canceled : order '.$order->getId());

        $route = 'customer_order_credit';
        if (OrderInterface::NATURE_CMD === $order->getNature()) {
            $route = 'customer_order_cmd';
        }

        return $this->redirectToRoute($route);
    }

    /**
     * Step2: Customer selects payment method.
     * TODO IL FAUT PASSER EN PARAMETRE LA COMMANDE
     * TODO IL FAUT VERIFIER QUE C'EST BIEN SA COMMANDE
     *
     * @Route("/method-choose", name="customer_payment_method")
     *
     * @param Request        $request        Request for form
     * @param OrderManager   $orderManager   Command manager
     * @param PaymentManager $paymentManager the payment manager to create payment entity
     * @param Payum          $payum          Payum manager
     *
     * @return Response|RedirectResponse
     *
     * @Security("is_granted('ROLE_USER')")
     */
    public function paymentMethod(
        Request $request,
        OrderManager $orderManager,
        PaymentManager $paymentManager,
        Payum $payum
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        try {
            //find carted (non canceled and non paid) and non empty order
            $order = $orderManager->getNonEmptyCartedOrder($user);
        } catch (NoOrderException $e) {
            //there is no order which is not empty, not canceled and no paid
            $this->addFlash('warning', 'flash.order.no-step1');

            return $this->redirectToRoute('customer_order_credit');
        }

        $methodModel = new PaymentMethod();
        $methodModel->acceptOffline($user->isAdmin());
        $form = $this->createForm(ChoosePaymentMethodType::class, $methodModel, [
            'offline' => $this->getUser()->isAdmin()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payment = $paymentManager->createPayment($payum, $order, [], $form->getData()->getMethod());

            $captureToken = $payum->getTokenFactory()->createCaptureToken(
                $form->getData()->getMethod(),
                $payment,
                'customer_payment_done'
            );

            return $this->redirect($captureToken->getTargetUrl());
        }

        return $this->render('payment/method-choose.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * Prepare and send mail.
     *
     * @param LoggerInterface $logger          logger interface to log alerts
     * @param MailerInterface $mailer          mailer interface to send mail
     * @param SettingsManager $settingsManager Setting manager to retrieve emails
     * @param Order           $order           order paid
     * @param Bill            $bill            billed bill
     */
    private function prepareAndSendMail(
        LoggerInterface $logger,
        MailerInterface $mailer,
        SettingsManager $settingsManager,
        Order $order,
        Bill $bill
    ): void {
        try {
            /** @var string $sender */
            $sender = $settingsManager->getValue('mail-sender');
            /** @var string $accountant */
            $accountant = $settingsManager->getValue('mail-accountant');

            $mailer->sendPaymentMail($order, $bill, $sender, $accountant);
        } catch (SettingsException $settingsException) {
            //the mail was not sent because parameters does not exists
            $logger->alert('Mail was not sent: '.$settingsException->getMessage());
        }
    }

    /**
     * Return the route for the payment method depending the nature of order.
     *
     * @param int|null $nature the nature of order
     */
    private function getPaymentMethodRoute(?int $nature): string
    {
        switch ($nature) {
            case OrderInterface::NATURE_OLSX:
                return 'customer_olsx_payment_method';
            case OrderInterface::NATURE_CMD:
                return 'customer_order_cmd';
            default:
                return 'customer_payment_method';
        }
    }
}
