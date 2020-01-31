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
use App\Model\TpeConfig;
use App\Repository\PaymentRepository;
use Ekyna\Component\Payum\Monetico\Api\Api;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param TpeConfig         $tpeConfig         the tpe config
     * @param Request           $request           the request
     * @param PaymentRepository $paymentRepository the payment repository to recover payment
     * @param OrderManager      $orderManager      the order manager to change its status
     * @param BillManager       $billManager       the bill manager to create bill
     *
     * @return Response
     */
    public function cic(
        TpeConfig $tpeConfig,
        Request $request,
        PaymentRepository $paymentRepository,
        OrderManager $orderManager,
        BillManager $billManager
    ): Response {
        $moneticoPayment = $this->payment($request);
        if (!$moneticoPayment->isValid($tpeConfig)) {
            return new Response(Api::NOTIFY_FAILURE);
        }

        if ($moneticoPayment->isPaymentCanceled()) {
            $this->log->info($moneticoPayment->formatLog());

            return new Response(Api::NOTIFY_SUCCESS);
        }

        $payumPayment = $paymentRepository->findOneByReference($moneticoPayment->getReference());
        if (!$payumPayment instanceof Payment) {
            $this->log->warning('Payum Payment NOT FOUND. '.$moneticoPayment->formatLog());

            return new Response(Api::NOTIFY_SUCCESS);
        }

        $order = $payumPayment->getOrder();
        if ($order->isPaid() && $order->isCredited()) {
            $this->log->warning('Order already paid and already credited! '.$moneticoPayment->formatLog());

            return new Response(Api::NOTIFY_SUCCESS);
        }

        $orderManager->validateAfterPaymentComplete($order);
        $bill = $billManager->retrieveOrCreateBill($order, $order->getCustomer());
        $orderManager->save($order);
        $billManager->save($bill);

        $this->log->info('Order paid and credited! '.$moneticoPayment->formatLog());

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
