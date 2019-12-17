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

use App\Entity\Bill;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Bill repository.
 *
 * @method Bill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bill[]    findAll()
 * @method Bill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillRepository extends ServiceEntityRepository
{
    /**
     * Bill repository constructor.
     *
     * @param RegistryInterface $registry provided by dependency injection
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bill::class);
    }

    /**
     * Find bills by order.
     *
     * @param Order $order order of searched bill
     *
     * @return array|Bill[]
     */
    public function findByOrder(Order $order): array
    {
        return $this->findBy(['order' => $order]);
    }

    /**
     * Return the max number in bills.
     *
     * @return int
     */
    public function maxNumber(): int
    {
        try {
            return (int) $this->createQueryBuilder('b')
                ->select('max(b.number) as maxi')
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NonUniqueResultException $exception) {
            //this should not be reached.
            return 0;
        }
    }
}
