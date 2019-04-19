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
use App\Exception\NoOrderException;
use App\Factory\BillFactory;
use App\Manager\BillManager;
use App\Manager\OrderManager;
use JMS\Payment\CoreBundle\Form\ChoosePaymentMethodType;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\Plugin\Exception\CommunicationException;
use JMS\Payment\CoreBundle\PluginController\Exception\InvalidPaymentInstructionException;
use JMS\Payment\CoreBundle\PluginController\PluginController;
use JMS\Payment\CoreBundle\PluginController\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentController extends AbstractController
{
    /**
     * Select credit.
     *
     * @Route("/payment/method-choose", name="customer_payment_method")
     *
     * @param Request             $request      Request for form
     * @param OrderManager        $orderManager Command manager
     * @param PluginController    $ppc          Plugin controller
     * @param TranslatorInterface $trans        the translator interface to translate data for Paypal page
     *
     * @throws InvalidPaymentInstructionException when choosing an invalid method
     *
     * @return Response|RedirectResponse
     */
    public function paymentMethod(
     Request $request,
     OrderManager $orderManager,
     PluginController $ppc,
     TranslatorInterface $trans
    ): Response {
        $user = $this->getUser();
        //TODO add token?
        $returnUrl = $this->generateUrl('customer_payment_create', [],UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('customer_payment_cancel', [],UrlGeneratorInterface::ABSOLUTE_URL);

        //find carted (non canceled and non paid) and non empty order
        try {
            $order = $orderManager->getNonEmptyCartedOrder($user);
        } catch (NoOrderException $e) {
            //there is no order which is not empty, not canceled and no paid
            $this->addFlash('warning', 'flash.order.no-step1');

            return $this->redirectToRoute('customer_order_credit');
        }

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
     * Payment create.
     *
     * @Route("/payment/create", name="customer_payment_create")
     *
     * @param BillManager      $billManager  bill manager to save bill
     * @param OrderManager     $orderManager order manager to retrieve order
     * @param PluginController $ppc          plugin controller
     *
     * @throws InvalidPaymentInstructionException on error with instruction
     *
     * @return RedirectResponse
     *
     * FIXME catch it!
     */
    public function paymentCreateAction(BillManager $billManager, OrderManager $orderManager, PluginController $ppc)
    {
        try {
            $user = $this->getUser();
            //FIXME Add a test on payment is not null!
            $order = $orderManager->getNonEmptyCartedOrder($user);
            $payment = $this->createPayment($order, $ppc);
            $result = $ppc->approveAndDeposit($payment->getId(), $payment->getTargetAmount());
        } catch (NoOrderException $noOrderException) {
            //there is no order which is not empty, not canceled and no paid
            return $this->redirectToRoute('customer_order_credit');
        } catch (CommunicationException $communicationException) {
            $this->addFlash('error', 'error.communication');

            return $this->redirectToRoute('customer_payment_method');
        }

        if (Result::STATUS_SUCCESS === $result->getStatus()) {
            $orderManager->setOrderPaid($order);
            $bill = BillFactory::create($order, $user);
            $orderManager->save($order);
            $billManager->save($bill);

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
     * Payment canceled!
     *
     * @Route("/payment-cancel", name="customer_payment_cancel")
     */
    public function paymentCancelAction()
    {
        //FIXME TADA !
        return new Response('Payment cancel');
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

    private function getPaypalCheckoutParams(Order $order, TranslatorInterface $trans): array
    {
        $paypalCheckoutParams = [
            'PAYMENTREQUEST_0_DESC' => $trans->trans('payment.paypal.description %credit% %amount%', [
                '%credit%' => $order->getCredits(),
                '%amount%' => $order->getAmount(), //FIXME localized number value
            ]),
            'PAYMENTREQUEST_0_ITEMAMT' => $order->getPrice(),
            'PAYMENTREQUEST_0_SHIPPINGAMT' => 0,
            'PAYMENTREQUEST_0_TAXAMT' => $order->getVat(),
            'PAYMENTREQUEST_0_SHIPDISCAMT' => 0,
        ];

        $itemNumber = 0;
        foreach ($order->getOrderedArticles() as $orderedArticle) {
            if ($orderedArticle->getQuantity()) {
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_AMT'.$itemNumber] = $orderedArticle->getArticle()->getPrice();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_QTY'.$itemNumber] = $orderedArticle->getQuantity();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_TAXAMT'.$itemNumber] = $orderedArticle->getArticle()->getVat();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_NAME'.$itemNumber] = $orderedArticle->getArticle()->getCode();
            }
        }

        return $paypalCheckoutParams;
    }
}
