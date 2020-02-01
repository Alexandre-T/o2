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
use App\Entity\Payment;
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
            ->setParameter('statusOrder', OrderInterface::STATUS_PAID)
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
     * Get carted non paid by user and nature is OLSX.
     *
     * @param User $user the customer
     */
    public function findOneByUserAndCartedOlsxCreditOrder(User $user): ?Order
    {
        return $this->findOneByUserNatureStatus(
            $user,
            OrderInterface::NATURE_OLSX,
            OrderInterface::STATUS_CARTED
        );
    }

    /**
     * Get carted non paid by user.
     *
     * @param User $user owner of command
     */
    public function findOneByUserAndCartedStandardCreditOrder(User $user): ?Order
    {
        return $this->findOneByUserNatureStatus(
            $user,
            OrderInterface::NATURE_CREDIT,
            OrderInterface::STATUS_CARTED
        );
    }

    /**
     * Find one order by uuid.
     *
     * @param string $uuid $uuid to retrieve order
     */
    public function findOneByUuid(string $uuid): ?Order
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    /**
     * Find one or no order filtered by customer, status and nature.
     *
     * @param User $user   the customer filter
     * @param int  $status the status filer
     */
    public function findByUserAndStatusOrder(User $user, int $status): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.statusOrder = :statusOrder')
            ->setParameter('statusOrder', $status)
            ->setParameter('customer', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find one or no order filtered by customer, status and nature.
     *
     * @param User $user   the customer filter
     * @param int  $nature the nature filter
     * @param int  $status the status filer
     */
    private function findOneByUserNatureStatus(User $user, int $nature, int $status): ?Order
    {
        try {
            //FIXME Change code and fix status to carted!
            return $this->createQueryBuilder('c')
                ->andWhere('c.customer = :customer')
                ->andWhere('c.statusOrder = :statusOrder')
                ->andWhere('c.nature = :nature')
                ->setParameter('statusOrder', $status)
                ->setParameter('customer', $user)
                ->setParameter('nature', $nature)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
