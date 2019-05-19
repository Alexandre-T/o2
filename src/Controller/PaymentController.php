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
use App\Exception\NoOrderException;
use App\Exception\SettingsException;
use App\Factory\BillFactory;
use App\Mailer\MailerInterface;
use App\Manager\BillManager;
use App\Manager\OrderManager;
use App\Manager\SettingsManager;
use Exception;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\Plugin\Exception\CommunicationException;
use JMS\Payment\CoreBundle\PluginController\Exception\InvalidPaymentInstructionException;
use JMS\Payment\CoreBundle\PluginController\PluginController;
use JMS\Payment\CoreBundle\PluginController\Result;
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
     * Payment completed!
     *
     * @Route("/complete/{uuid}", name="customer_payment_complete")
     *
     * @param BillManager     $billManager     bill manager to generate bill
     * @param LoggerInterface $logger          logger added to alert when settings are wrong
     * @param MailerInterface $mailer          mailer interface to sent mail to admin
     * @param OrderManager    $orderManager    order manager to retrieve order
     * @param Request         $request         to retrieve token and payerId
     * @param SettingsManager $settingsManager to retrieve mail of sender and receiver
     * @param string          $uuid            to retrieve order
     *
     * @return Response
     */
    public function paymentComplete(
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

            $payerId = $request->get('PayerID');
            if (null !== $payerId) {
                $order->setPayerId($payerId);
            }

            $token = $request->get('token');
            if (null !== $token) {
                $order->setToken($token);
            }

            //Save order
            $orderManager->validateAfterPaymentComplete($order);
            $bill = $billManager->retrieveOrCreateBill($order, $this->getUser());
            $orderManager->save($order);
            $billManager->save($bill);

            //Mail sending
            $this->prepareAndSendMail($logger, $mailer, $settingsManager, $order, $bill);
        } catch (NoOrderException $noOrderException) {
            $this->addFlash('error', 'error.payment.non-existent');

            return $this->redirectToRoute('home');
        }

        return $this->render('payment/complete.html.twig', [
            'paymentSystemName' => $order->getPaymentInstruction()->getPaymentSystemName(),
            'order' => $order,
        ]);
    }

    /**
     * Step3: Customer want to pay.
     *
     * @Route("/create", name="customer_payment_create")
     *
     * @param BillManager      $billManager     bill manager to save bill
     * @param OrderManager     $orderManager    order manager to retrieve order
     * @param PluginController $ppc             plugin controller
     * @param LoggerInterface  $logger          logger interface
     * @param MailerInterface  $mailer          mailer interface to send mail
     * @param SettingsManager  $settingsManager setting manager to retrieve emails
     *
     * @return RedirectResponse
     *
     * @Security("is_granted('ROLE_USER')")
     */
    public function paymentCreateAction(
     BillManager $billManager,
     OrderManager $orderManager,
     PluginController $ppc,
     LoggerInterface $logger,
     MailerInterface $mailer,
     SettingsManager $settingsManager
    ): RedirectResponse {
        try {
            $user = $this->getUser();
            $order = $orderManager->getNonEmptyCartedOrder($user);
            $payment = $this->createPayment($order, $ppc);
            $result = $ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        } catch (NoOrderException $noOrderException) {
            //there is no order which is not empty, not canceled and no paid
            return $this->redirectToRoute('customer_order_credit');
        } catch (CommunicationException $comException) {
            $this->addFlash('error', 'error.communication');
            $logger->error('Communication error: '.$comException->getMessage());

            return $this->redirectToRoute('customer_payment_method');
        } catch (InvalidPaymentInstructionException $exception) {
            $this->addFlash('error', 'error.payment-instruction');
            $logger->alert('Payment Instruction error: '.$exception->getMessage());

            return $this->redirectToRoute('customer_payment_method');
        }

        if (Result::STATUS_SUCCESS === $result->getStatus()) {
            $orderManager->credit($order);
            $orderManager->setPaid($order);
            $bill = BillFactory::create($order, $user);
            $orderManager->save($order);
            $billManager->save($bill);

            //Mail sending
            $this->prepareAndSendMail($logger, $mailer, $settingsManager, $order, $bill);

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

        // In a real-world application I don't throw the exception.
        //throw $result->getPluginException();
        $exception = $result->getPluginException();

        if ($exception instanceof Exception) {
            $logger->alert('Erreur de paiement: '.$exception->getMessage());
        }

        return $this->redirectToRoute('home');
    }

    /**
     * Step2: Customer selects payment method.
     *
     * @Route("/method-choose", name="customer_payment_method")
     *
     * @param Request             $request      Request for form
     * @param OrderManager        $orderManager Command manager
     * @param PluginController    $ppc          Plugin controller
     * @param TranslatorInterface $trans        the translator interface to translate data for Paypal page
     *
     * @throws InvalidPaymentInstructionException when choosing an invalid method
     *
     * @return Response|RedirectResponse
     *
     * @Security("is_granted('ROLE_USER')")
     */
    public function paymentMethod(
     Request $request,
     OrderManager $orderManager,
     PluginController $ppc,
     TranslatorInterface $trans
    ): Response {
        $user = $this->getUser();
        try {
            //find carted (non canceled and non paid) and non empty order
            $order = $orderManager->getNonEmptyCartedOrder($user);
            $returnUrl = $this->generateUrl(
                'customer_payment_complete',
                ['uuid' => $order->getUuid()],
                UrlGeneratorInterface::ABSOLUTE_URL);
            $cancelUrl = $this->generateUrl(
                'customer_payment_cancel',
                ['order' => $order->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (NoOrderException $e) {
            //there is no order which is not empty, not canceled and no paid
            $this->addFlash('warning', 'flash.order.no-step1');

            return $this->redirectToRoute('customer_order_credit');
        }

        //TODO Move this in a private method.
        $paypalCheckoutParams = $this->getPaypalCheckoutParams($order, $trans);

        $predefinedData = [
            'paypal_express_checkout' => [
                'checkout_params' => $paypalCheckoutParams,
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
            //stripe_checkout, etc.
        ];

        $form = $this->createForm(ChoosePaymentMethodType::class, null, [
            'amount' => $order->getAmount(),
            'currency' => 'EUR',
            'default_method' => 'paypal_express_checkout',
            'predefined_data' => $predefinedData,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //We know how customer want to pay
            $ppc->createPaymentInstruction($instruction = $form->getData());
            $order->setPaymentInstruction($instruction);
            $orderManager->save($order);

            return $this->redirectToRoute('customer_payment_create');
        }

        return $this->render('payment/method-choose.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
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
     *
     * @Security("is_granted('ROLE_USER')")
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
     *
     */
    private function prepareAndSendMail(
     LoggerInterface $logger,
     MailerInterface $mailer,
     SettingsManager $settingsManager,
     Order $order,
     Bill $bill
    ):void {
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
