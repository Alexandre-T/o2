<?php

namespace App\Action;

use App\Entity\Order;
use Payum\Core\GatewayAwareTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Abstract Convert Payment Action
 * This abstract class initialize the translator and the url generator.
 * It provides method to get cancel and return URLs.
 */
abstract class AbstractConvertAction
{
    use GatewayAwareTrait;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    /**
     * @var UrlGeneratorInterface
     */
    protected UrlGeneratorInterface $urlGenerator;

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
     * Return the url when payment is done.
     *
     * @return string
     */
    protected function getReturnUrl(): string
    {
        return $this->urlGenerator->generate(
            'customer_payment_done',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Return the cancel URL.
     *
     * @param Order $order The order to redirect customer on cancel.
     *
     * @return string
     */
    protected function getCancelUrl(Order $order): string
    {
        return $this->urlGenerator->generate(
            'customer_payment_cancel',
            ['order' => $order->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}