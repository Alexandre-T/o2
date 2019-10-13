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
     * Payment manager constructor.
     *
     * @param EntityManagerInterface $entityManager entity manager provided by dependency injection
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Payment::class);
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
}
