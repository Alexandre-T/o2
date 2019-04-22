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

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use App\Model\OrderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Order repository.
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * OrderRepository constructor.
     *
     * @param RegistryInterface $registry registry is injected by DI
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Count orders for user and code provided.
     *
     * @param User $user        user filter
     * @param int  $statusOrder status order filter
     *
     * @return int
     */
    public function countByUserAndStatusOrder(User $user, int $statusOrder): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        try {
            return $queryBuilder
                ->select($queryBuilder->expr()->count('1'))
                ->where('o.customer = :customer')
                ->andWhere('o.statusOrder = :statusOrder')
                ->setParameter('customer', $user)
                ->setParameter('statusOrder', $statusOrder)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NonUniqueResultException $e) {
            //This code could not be reached.
            return 0;
        }
    }

    /**
     * Get order for user and code provided.
     *
     * @param User $user        user filter
     * @param int  $statusOrder status order filter
     *
     * @return Order[]
     */
    public function findByUserAndStatusOrder(User $user, int $statusOrder): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where('o.customer = :customer')
            ->andWhere('o.code = :code')
            ->setParameter('customer', $user)
            ->setParameter('statusOrder', $statusOrder)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Get order for user and code provided.
     *
     * @param User $user user filter
     * @param int  $code code filter
     *
     * @return Order[]
     */
    public function findByUserNonEmptyStatusOrder(User $user, int $code): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where('o.customer = :customer')
            ->andWhere('o.statusOrder = :statusOrder')
            ->andWhere('o.price > 0')
            ->setParameter('customer', $user)
            ->setParameter('statusOrder', $code)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Find the last one paid order.
     *
     * @return Order|null
     */
    public function findLastPaid(): ?Order
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $orders = $queryBuilder
            ->where('o.customer = :customer')
            ->andWhere('o.statusOrder = :statusOrder')
            ->andWhere('o.amount > 0')
            ->orderBy('o.paymentAt', 'desc')
            ->setMaxResults(1)
            ->setParameter('statusOrder', OrderInterface::PAID)
            ->getQuery()
            ->getResult()
        ;

        if (empty($orders)) {
            return null;
        }

        return $orders[0];
    }

    /**
     * Find one order by its payment instruction.
     *
     * @param PaymentInstructionInterface $paymentInstruction linked payment instruction
     *
     * @return Order|null
     */
    public function findOneByPaymentInstruction(PaymentInstructionInterface $paymentInstruction): ?Order
    {
        return $this->findOneBy(['paymentInstruction' => $paymentInstruction]);
    }

    /**
     * Get carted non paid by user.
     *
     * @param User $customer owner of command
     *
     * @return Order|null
     */
    public function findOneByUserAndCarted(User $customer): ?Order
    {
        try {
            return $this->createQueryBuilder('c')
                ->andWhere('c.customer = :customer')
                ->andWhere('c.statusOrder = :statusOrder')
                ->setParameter('statusOrder', OrderInterface::CARTED)
                ->setParameter('customer', $customer)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Find one order by uuid.
     *
     * @param string $uuid $uuid to retrieve order
     *
     * @return Order|null
     */
    public function findOneByUuid(string $uuid): ?Order
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }
}
