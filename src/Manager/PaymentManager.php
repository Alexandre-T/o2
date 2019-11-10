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

namespace App\Manager;

use App\Entity\Order;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Payum;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Payment Manager.
 */
class PaymentManager
{
    /**
     * The entity manager interface.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * The payment repository.
     *
     * @var PaymentRepository
     */
    private $repository;

    /**
     * The translator interface to translate items for Paypal.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Payment manager constructor.
     *
     * @param EntityManagerInterface $entityManager entity manager provided by dependency injection
     * @param TranslatorInterface    $translator    the translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Payment::class);
        $this->translator = $translator;
    }

    /**
     * Return the last payment.
     *
     * @param Order $order the order to find payments
     *
     * @return Payment|null
     */
    public function getValidPayment(Order $order): ?Payment
    {
        $payments = $this->repository->findByOrder($order);

        foreach ($payments as $payment) {
            return $payment;
        }

        return null;
    }

    /**
     * Create a payment.
     *
     * @param Payum  $payum       Payum manager
     * @param Order  $order       The order contain all elements
     * @param array  $details     The details contains information about url
     * @param string $description The description (the method)
     *
     * @return Payment
     */
    public function createPayment(Payum $payum, Order $order, array $details, string $description): Payment
    {
        $storage = $payum->getStorage(Payment::class);
        /** @var Payment $payment */
        $payment = $storage->create();
        $payment->setNumber(substr(uniqid(), 0, 12));
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount((int) ($order->getAmount() * 100));
        $payment->setDescription($description);
        $payment->setClientId($order->getCustomer()->getId());
        $payment->setClientEmail($order->getCustomer()->getMail());
        $payment->setDetails($details);

        $payment->setOrder($order);
        $storage->update($payment);

        return $payment;
    }

    /**
     * Return the paypal checkout params.
     *
     * @param Order $order the order
     *
     * @return array
     */
    public function getPaypalCheckoutParams(Order $order): array
    {
        $paypalCheckoutParams = [
            'PAYMENTREQUEST_0_DESC' => $this->translator->trans('payment.paypal.description %credit% %amount%', [
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
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_AMT'.$item] = $orderedArticle->getPrice();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_QTY'.$item] = $orderedArticle->getQuantity();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_TAXAMT'.$item] = $orderedArticle->getVat();
                $paypalCheckoutParams['L_PAYMENTREQUEST_0_NAME'.$item] = $this->translator->trans(
                    "article.{$orderedArticle->getArticle()->getCode()}.text"
                );
                ++$item;
            }
        }

        return $paypalCheckoutParams;
    }
}
