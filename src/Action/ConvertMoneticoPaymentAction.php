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

use App\Entity\OrderedArticle;
use App\Entity\Payment;
use App\Model\OrderInterface;
use Doctrine\Common\Collections\Collection;
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
 * Class ConvertPaymentAction.
 */
class ConvertMoneticoPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

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
        if (empty($model['reference'])) {
            $model['reference'] = (string) $payment->getNumber();
        }

        if (empty($model['amount'])) {
            $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));
            $amount = (string) $payment->getTotalAmount();
            if (0 < $currency->exp) {
                $divisor = 10 ** $currency->exp;
                $amount = (string) round($amount / $divisor, $currency->exp);
                $pos = strpos($amount, '.');
                if (false !== $pos) {
                    $amount = str_pad($amount, $pos + 1 + $currency->exp, '0', STR_PAD_RIGHT);
                }
            }

            $model['amount'] = $amount;
            $model['currency'] = (string) strtoupper($currency->code);
        }

        if (empty($model['email'])) {
            $model['email'] = $payment->getClientEmail();
        }

        if (empty($model['comment'])) {
            $model['comment'] = 'Customer: '.$payment->getClientId();
            $model['comment'] .= ',Order: '.$payment->getOrder()->getId();
        }

        // The 3DSecure v2 require that you provide the order context.
        // @see the Monetico paiement documentation page 73
        // https://www.monetico-paiement.fr/fr/info/documentations/Monetico_Paiement_documentation_technique_v2.1.pdf
        $model['context'] = [
            'billing' => [
                'addressLine1' => $user->getStreetAddress(),
                'addressLine2' => $user->getComplement(),
                'name' => $user->getLabel(),
                'city' => $user->getLocality(),
                'postalCode' => $user->getPostalCode(),
                'country' => $user->getCountry(),
            ],
            'shoppingCart' => [
                'preorderIndicator' => false,
                'reorderIndicator' => false,
                'shoppingCartItems' => $this->articlesToArray($order->getOrderedArticles()),
            ],
            'client' => [
                'addressLine1' => $user->getStreetAddress(),
                'addressLine2' => $user->getComplement(),
                'name' => $user->getLabel(),
                'city' => $user->getLocality(),
                'postalCode' => $user->getPostalCode(),
                'country' => $user->getCountry(),
                'email' => $user->getMail(),
            ],
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
            && 'array' === $request->getTo();
    }

    /**
     * Transform OrderedArticle[] into shoppingCartItems array.
     *
     * @see https://www.monetico-paiement.fr/fr/info/documentations/Monetico_Paiement_documentation_technique_v2.1.pdf
     * Page 75
     *
     * @param Collection|OrderedArticle[] $orderedArticles the collection of ordered article
     */
    private function articlesToArray(Collection $orderedArticles): array
    {
        $shoppingCarItems = [];

        foreach ($orderedArticles as $orderedArticle) {
            if ($orderedArticle->getQuantity() > 0) {
                $shoppingCarItems[] = $this->articleToArray($orderedArticle);
            }
        }

        return $shoppingCarItems;
    }

    /**
     * Transform OrderedArticle into shoppingCartItem.
     *
     * @see https://www.monetico-paiement.fr/fr/info/documentations/Monetico_Paiement_documentation_technique_v2.1.pdf
     * Page 75
     *
     * @param OrderedArticle $orderedArticle the ordered article
     */
    private function articleToArray(OrderedArticle $orderedArticle)
    {
        $code = $orderedArticle->getArticle()->getCode();

        return [
            'name' => $this->translator->trans(sprintf('article.%s.text', $code)),
            'description' => $this->translator->trans(sprintf('article.%s.title', $code)),
            'productCode' => $this->getProductCode($orderedArticle->getOrder()->getNature()),
            'unitPrice' => (int) ($orderedArticle->getAmount() * 100), //in cents of euro
            'quantity' => $orderedArticle->getQuantity(),
            'productSKU' => $code,
            'productRisk' => 'low',
        ];
    }

    /**
     * Return service or gift_certificate depending the nature of order.
     *
     * @param int|null $nature the nature of order
     */
    private function getProductCode(?int $nature): string
    {
        if (OrderInterface::NATURE_CMD === $nature) {
            return 'service';
        }

        return 'gift_certificate';
    }
}
