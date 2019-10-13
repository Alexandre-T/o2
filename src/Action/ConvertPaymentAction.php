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

namespace App\Action;

use App\Entity\Payment;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetCurrency;

/**
 * Class ConvertPaymentAction.
 */
class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * Improved payment details to provide information needed by monetico gateway.
     *
     * @param Convert $request the request implements Convert because of support method
     *
     * @throws RequestNotSupportedException if the action dose not support the request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        /** @var Payment $payment */
        $payment = $request->getSource();
        $order = $payment->getOrder();
        $user = $order->getCustomer();
        $model = ArrayObject::ensureArrayObject($payment->getDetails());
        if (false == $model['reference']) {
            $model['reference'] = (string) $payment->getNumber();
        }

        if (false == $model['amount']) {
            $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));
            $amount = (string) $payment->getTotalAmount();
            if (0 < $currency->exp) {
                $divisor = 10 ** $currency->exp;
                $amount = (string) round($amount / $divisor, $currency->exp);
                if (false !== $pos = strpos($amount, '.')) {
                    $amount = str_pad($amount, $pos + 1 + $currency->exp, '0', STR_PAD_RIGHT);
                }
            }

            $model['amount'] = $amount;
            $model['currency'] = (string) strtoupper($currency->code);
        }

        if (false == $model['email']) {
            $model['email'] = $payment->getClientEmail();
        }

        if (false == $model['comment']) {
            $model['comment'] = 'Customer: '.$payment->getClientId();
            $model['comment'] .= ',Order: '.$payment->getOrder()->getId();
        }

        // The 3DSecure v2 require that you provide the order context.
        // @see the Monetico paiement documentation page 73
        // https://www.monetico-paiement.fr/fr/info/documentations/Monetico_Paiement_documentation_technique_v2.1.pdf
        $model['context'] = [
            'billing' => [
                'addressLine1' => $user->getStreetAddress(),
                'city' => $user->getLocality(),
                'postalCode' => $user->getPostalCode(),
                'country' => $user->getCountry(),
            ],
            // TODO add shopping cart and client information
        ];

        $request->setResult((array) $model);
    }

    /**
     * Verify that request is an instance of Convert and its source is an interface of Payment.
     *
     * @param mixed $request the request to test
     *
     * @return bool
     */
    public function supports($request)
    {
        return $request instanceof Convert
            && $request->getSource() instanceof PaymentInterface
            && 'array' == $request->getTo();
    }
}
