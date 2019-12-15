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
use App\Entity\Payment as Payment;
use App\Entity\User;
use App\Model\OrderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

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
     * @param ManagerRegistry $registry registry is injected by DI
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Get order for user and code provided.
     *
     * @param User $user        user filter
     * @param int  $statusOrder status order filter
     *
     * @return Order[]
     */
    public function findByUserAndStatusCreditOrder(User $user, int $statusOrder): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where('o.customer = :customer')
            ->andWhere('o.code = :code')
            ->andWhere('o.nature = :nature')
            ->setParameter('customer', $user)
            ->setParameter('statusOrder', $statusOrder)
            ->setParameter('nature', OrderInterface::NATURE_CREDIT)
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
    public function findByUserNonEmptyStatusCreditOrder(User $user, int $code): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where('o.customer = :customer')
            ->andWhere('o.statusOrder = :statusOrder')
            ->andWhere('o.nature = :nature')
            ->andWhere('o.price > 0')
            ->setParameter('customer', $user)
            ->setParameter('statusOrder', $code)
            ->setParameter('nature', OrderInterface::NATURE_CREDIT)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Get order for user and code provided.
     *
     * @param User $user        user filter
     * @param int  $statusOrder status order filter
     *
     * @return Order[]
     */
    public function findCmdByUserAndStatusOrder(User $user, int $statusOrder): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->join('o.orderedArticles', 'oa')
            ->where('o.customer = :customer')
            ->andWhere('o.statusOrder = :statusOrder')
            ->andWhere('o.price > 0')
            ->andWhere('o.nature = :nature')
            ->setParameter('customer', $user)
            ->setParameter('statusOrder', $statusOrder)
            ->setParameter('nature', Order::NATURE_CMD)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Find the last one paid order.
     *
     * Code not used!
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
     * Find one order by payment.
     *
     * @param Payment $payment the linked payment
     *
     * @return Order|null
     */
    public function findOneByPayment(Payment $payment)
    {
        return $this->findOneBy(['payment' => $payment]);
    }

    /**
     * Get carted non paid by user.
     *
     * @param User $customer owner of command
     *
     * @return Order|null
     */
    public function findOneByUserAndCartedCreditOrder(User $customer): ?Order
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
