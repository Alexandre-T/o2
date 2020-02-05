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

use App\Entity\Payment;
use App\Manager\BillManager;
use App\Manager\OrderManager;
use App\Model\MoneticoPayment;
use App\Repository\PaymentRepository;
use Ekyna\Component\Payum\Monetico\Api\Api;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The CIC Controller is used to catch Monetico notifications.
 */
class CicController
{
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * CicController constructor.
     *
     * @param LoggerInterface $log the logger for tpe
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Return from cic bank.
     *
     * @Route("/retour-cic", name="cic-return", methods={"post", "get"})
     *
     * @param Request           $request           the request
     * @param PaymentRepository $paymentRepository the payment repository to recover payment
     * @param OrderManager      $orderManager      the order manager to change its status
     * @param BillManager       $billManager       the bill manager to create bill
     */
    public function cic(
        Request $request,
        PaymentRepository $paymentRepository,
        OrderManager $orderManager,
        BillManager $billManager
    ): Response {
        $moneticoPayment = $this->payment($request);

        //TODO Add a test to verify request is not valid

        if ($moneticoPayment->isPaymentCanceled()) {
            $this->log->info($moneticoPayment->formatLog());

            return new Response(Api::NOTIFY_SUCCESS);
        }

        if (null === $moneticoPayment->getReference()) {
            $this->log->warning('Monetico request is not complete');

            return new Response(Api::NOTIFY_FAILURE);
        }

        $payumPayment = $paymentRepository->findOneByReference($moneticoPayment->getReference());
        if (!$payumPayment instanceof Payment) {
            $this->log->warning('Payum Payment NOT FOUND. '.$moneticoPayment->formatLog());

            return new Response(Api::NOTIFY_SUCCESS);
        }

        $order = $payumPayment->getOrder();
        if (null === $order) {
            $this->log->error('Payum has no order');

            return new Response(Api::NOTIFY_FAILURE);
        }

        if ($order->isPaid() && $order->isCredited()) {
            $this->log->warning('Order already paid and already credited! '.$moneticoPayment->formatLog());

            return new Response(Api::NOTIFY_SUCCESS);
        }

        if ($moneticoPayment->isPaymentOk()) {
            $orderManager->validateAfterPaymentComplete($order);
            $bill = $billManager->retrieveOrCreateBill($order, $order->getCustomer());
            $orderManager->save($order);
            $billManager->save($bill);

            $this->log->info('Order paid and credited! '.$moneticoPayment->formatLog());

            return new Response(Api::NOTIFY_SUCCESS);
        }

        $this->log->info('Payement was canceled by user! '.$moneticoPayment->formatLog());

        return new Response(Api::NOTIFY_SUCCESS);
    }

    /**
     * Return a monetico Payment constructed via Request.
     *
     * @param Request $request request coming from monetico API
     */
    private function payment(Request $request): MoneticoPayment
    {
        return new MoneticoPayment($request);
    }
}
