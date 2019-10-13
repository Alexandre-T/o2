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
use App\Exception\NoOrderException;
use App\Exception\SettingsException;
use App\Form\ChoosePaymentMethodType;
use App\Form\Model\PaymentMethod;
use App\Mailer\MailerInterface;
use App\Manager\BillManager;
use App\Manager\OrderManager;
use App\Manager\SettingsManager;
use Exception;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @Route("/analyse", name="customer_payment_done")
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
        } catch (Exception $e) {
            //I do not have the token.
            $this->addFlash('error', 'error.payment.non-existent');
            $log->log(
                LogLevel::WARNING,
                'This order does not exists or has been already paid. Token does not exist.'
            );

            return $this->redirectToRoute('customer_order_credit');
        }

        $gatewayName = $token->getGatewayName();
        $gateway = $payum->getGateway($gatewayName);

        // you can invalidate the token. The url could not be requested any more.
        // $payum->getHttpRequestVerifier()->invalidate($token);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        /** @var Payment $payment */
        $payment = $status->getFirstModel();
        $order = $payment->getOrder();

        if (!$status->isAuthorized() && !$status->isPending()) {
            $this->addFlash('warning', 'error.payment.canceled');

            $log->log(
                LogLevel::INFO,
                "Payment{$payment->getId()} for order{$order->getId()} canceled (payum status: {$status->getValue()})"
            );

            return $this->redirectToRoute('customer_payment_method');
        }

        //Save order
        $orderManager->validateAfterPaymentComplete($order);
        $bill = $billManager->retrieveOrCreateBill($order, $this->getUser());
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

        return $this->redirectToRoute('customer_order_credit');
    }

    /**
     * Payment completed via CB.
     *
     * @Route("/complete/{uuid}", name="customer_payment_complete")
     *
     * @param Payum           $payum           Payum manager
     * @param BillManager     $billManager     bill manager to generate bill
     * @param LoggerInterface $logger          logger added to alert when settings are wrong
     * @param MailerInterface $mailer          mailer interface to sent mail to admin
     * @param OrderManager    $orderManager    order manager to retrieve order
     * @param Request         $request         to retrieve token and payerId
     * @param SettingsManager $settingsManager to retrieve mail of sender and receiver
     * @param string          $uuid            to retrieve order
     *
     * @return response
     *
     * NO SECURITY because user can logout just before payment
     */
    public function paymentComplete(
     Payum $payum,
     BillManager $billManager,
     LoggerInterface $logger,
     MailerInterface $mailer,
     OrderManager $orderManager,
     Request $request,
     SettingsManager $settingsManager,
     string $uuid
    ): Response {
        try {
            $order = $orderManager->retrieveByUuid($uuid);
        } catch (NoOrderException $noOrderException) {
            $this->addFlash('error', 'error.payment.non-existent');

            $logger->log(
                LogLevel::WARNING,
                'This order does not exists or has been already paid. Token does not exist.'
            );

            return $this->redirectToRoute('home');
        }

        try {
            $token = $payum->getHttpRequestVerifier()->verify($request);
            $gatewayName = $token->getGatewayName();
            $gateway = $payum->getGateway($gatewayName);
            $gateway->execute(/*$status =*/ new GetHumanStatus($token));
        } catch (Exception $e) {
            $logger->warning('TOKEN INCONNU pour la commande $uuid');
            $token = 'unknown';
            $gatewayName = 'unknown';
        }

        //TODO use status to save order only if it was not already completed by notification.
        //Save order
        $orderManager->validateAfterPaymentComplete($order);
        $bill = $billManager->retrieveOrCreateBill($order, $this->getUser());
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

    /**
     * Step2: Customer selects payment method.
     *
     * @Route("/method-choose", name="customer_payment_method")
     *
     * @param Request             $request      Request for form
     * @param OrderManager        $orderManager Command manager
     * @param Payum               $payum        Payum manager
     * @param TranslatorInterface $trans        the translator interface to translate data for Paypal page
     *
     * @return Response|RedirectResponse
     *
     * @Security("is_granted('ROLE_USER')")
     */
    public function paymentMethod(
     Request $request,
     OrderManager $orderManager,
     Payum $payum,
     TranslatorInterface $trans
    ): Response {
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
        $form = $this->createForm(ChoosePaymentMethodType::class, $methodModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $storage = $payum->getStorage(Payment::class);
            /** @var Payment $payment */
            $payment = $storage->create();
            $payment->setNumber(substr(uniqid(), 0, 12));
            $payment->setCurrencyCode('EUR');
            $payment->setTotalAmount((int) ($order->getAmount() * 100));
            $payment->setDescription($form->getData()->getMethod());
            $payment->setClientId($this->getUser()->getId());
            $payment->setClientEmail($this->getUser()->getMail());
            $details = [];

            //Get routes
            $returnUrl = $this->generateUrl(
                'customer_payment_complete',
                ['uuid' => $order->getUuid()],
                UrlGeneratorInterface::ABSOLUTE_URL);
            $cancelUrl = $this->generateUrl(
                'customer_payment_cancel',
                ['order' => $order->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL);
            $analyseUrl = $this->generateUrl(
                'customer_payment_done',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL);

            if ('paypal_express_checkout' === $form->getData()->getMethod()) {
                $details = array_merge($details, $this->getPaypalCheckoutParams($order, $trans));
                $details['cancel_url'] = $cancelUrl;
                $details['return_url'] = $returnUrl;
            }

            if ('monetico' === $form->getData()->getMethod()) {
                $details['success_url'] = $returnUrl;
                $details['failure_url'] = $cancelUrl;
            }

            $payment->setDetails($details);

            $payment->setOrder($order);
            $storage->update($payment);

            $captureToken = $payum->getTokenFactory()->createCaptureToken(
                $form->getData()->getMethod(),
                $payment,
                $analyseUrl
            );

            return $this->redirect($captureToken->getTargetUrl());
        }

        return $this->render('payment/method-choose.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    /**
     * Paypal checkout params getter.
     *
     * @param Order               $order order
     * @param TranslatorInterface $trans to translate description for paypal
     *
     * @return array
     */
    private function getPaypalCheckoutParams(Order $order, TranslatorInterface $trans): array
    {
        $paypalCheckoutParams = [
            'PAYMENTREQUEST_0_DESC' => $trans->trans('payment.paypal.description %credit% %amount%', [
                '%credit%' => $order->getCredits(),
                '%amount%' => $order->getAmount(),
            ]),
            'PAYMENTREQUEST_0_ITEMAMT' => $order->getPrice(),
            'PAYMENTREQUEST_0_SHIPPINGAMT' => 0,
            'PAYMENTREQUEST_0_TAXAMT' => $order->getVat(),
            'PAYMENTREQUEST_0_SHIPDISCAMT' => 0,
        ];

        $item = 0;
        foreach ($order->getOrderedArticles() as $orderedArticle) {
            if ($orderedArticle->getQuantity()) {
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_AMT'.$item] = $orderedArticle->getArticle()->getPrice();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_QTY'.$item] = $orderedArticle->getQuantity();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_TAXAMT'.$item] = $orderedArticle->getArticle()->getVat();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_NAME'.$item] = $trans->trans(
                    "article.{$orderedArticle->getArticle()->getCode()}.text"
                );
                ++$item;
            }
        }

        return $paypalCheckoutParams;
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
}
