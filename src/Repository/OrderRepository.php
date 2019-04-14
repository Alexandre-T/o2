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
use App\Entity\StatusOrder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
                ->innerJoin('c.statusOrder', 's')
                ->andWhere('c.customer = :customer')
                ->andWhere('s.code = :code')
                ->setParameter('code', StatusOrder::CARTED)
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
     * Count orders for user and code provided.
     *
     * @param User   $user user filter
     * @param string $code code filter
     *
     * @return int
     */
    public function countByUserAndStatusOrder(User $user, string $code): int
    {
        $queryBuilder = $this->createQueryBuilder('o');

        try {
            return $queryBuilder
                ->select($queryBuilder->expr()->count('1'))
                ->innerJoin('o.statusOrder', 's')
                ->where('o.customer = :customer')
                ->andWhere('s.code = :code')
                ->setParameter('customer', $user)
                ->setParameter('code', $code)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NonUniqueResultException $e) {
            //This code could not be reached.
            return 0;
        }
    }
}
