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

use App\Entity\StatusOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Status order repository.
 *
 * Status orders are not an entity, but a resource.
 *
 * @method StatusOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatusOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatusOrder[]    findAll()
 * @method StatusOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusOrderRepository extends ServiceEntityRepository
{
    /**
     * Status order repository constructor.
     *
     * @param RegistryInterface $registry registry interface provided by dependency injection
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StatusOrder::class);
    }

    /**
     * Find a status order by code.
     *
     * @param string $code code to find
     *
     * @return StatusOrder|null
     */
    public function findOneByCode(string $code): ?StatusOrder
    {
        $queryBuilder = $this->createQueryBuilder('s');
        try {
            return $queryBuilder->where('s.code = :code')
                ->setParameter('code', $code)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            // this cannot be reached.
            return null;
        }
    }

    /**
     * Find the PAID status order.
     *
     * @return StatusOrder|null
     */
    public function findOnePaid(): ?StatusOrder
    {
        return $this->findOneByCode(StatusOrder::PAID);
    }
}
