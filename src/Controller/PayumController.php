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
use Payum\Core\Payum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Payment controller .
 *
 * @Route("/payum", name="payum_")
 */
class PayumController extends AbstractController
{
    /**
     * Prepare the payment.
     *
     * @param Payum $payumManager the payum manager to create payment
     *
     * @Route("/method-choose", name="prepare")
     */
    public function prepare(Payum $payumManager)
    {
        $gatewayName = 'offline';

        $storage = $payumManager->getStorage(Payment::class);

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(123); // 1.23 EUR
        $payment->setDescription('A description');
        $payment->setClientId('anId');
        $payment->setClientEmail('foo@example.com');

        $storage->update($payment);

        $captureToken = $payumManager->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'payum_done' // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());
    }
}
