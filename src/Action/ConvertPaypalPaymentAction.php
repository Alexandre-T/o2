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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConvertPaypalPaymentAction.
 */
class ConvertPaypalPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * ConvertPaymentAction constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator the url generator
     * @param TranslatorInterface   $translator   the translator to provide name of cart items
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * Improved payment details to provide information needed by paypal_express_checkout gateway.
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
        $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));
        $divisor = 10 ** $currency->exp;

        $details = ArrayObject::ensureArrayObject($payment->getDetails());
        $details['INVNUM'] = $payment->getNumber();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = $payment->getCurrencyCode();
        $details['PAYMENTREQUEST_0_AMT'] = $payment->getTotalAmount() / $divisor;

        $details['PAYMENTREQUEST_0_DESC'] = $this->translator->trans(
            'payment.paypal.description %credit% %amount%',
            [
                '%credit%' => $order->getCredits(),
                '%amount%' => $order->getAmount(),
            ]
        );

        $details['PAYMENTREQUEST_0_ITEMAMT'] = $order->getPrice();
        $details['PAYMENTREQUEST_0_SHIPPINGAMT'] = 0;
        $details['PAYMENTREQUEST_0_TAXAMT'] = $order->getVat();
        $details['PAYMENTREQUEST_0_SHIPDISCAMT'] = 0;

        $item = 0;
        foreach ($order->getOrderedArticles() as $orderedArticle) {
            if ($orderedArticle->getQuantity()) {
                $details['L_PAYMENTREQUEST_0_AMT'.$item] = $orderedArticle->getPrice();
                $details['L_PAYMENTREQUEST_0_QTY'.$item] = $orderedArticle->getQuantity();
                $details['L_PAYMENTREQUEST_0_TAXAMT'.$item] = $orderedArticle->getVat();
                $details['L_PAYMENTREQUEST_0_NAME'.$item] = $this->translator->trans(
                    "article.{$orderedArticle->getArticle()->getCode()}.text"
                );
                ++$item;
            }
        }

        $request->setResult($details);
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
            && 'array' === $request->getTo();
    }
}
