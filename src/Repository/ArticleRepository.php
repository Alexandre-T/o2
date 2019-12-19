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

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Article repository.
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    /**
     * ArticleRepository constructor.
     *
     * @param ManagerRegistry $registry injected by dependency injection
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Find bills by order.
     *
     * @return array|Article[]
     */
    public function findCredit(): array
    {
        $queryBuilder = $this->createQueryBuilder('a');

        return $queryBuilder->where('a.code LIKE :code')
            ->setParameter('code', 'CRED%')
            ->orderBy('a.price')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Find one article by its code.
     *
     * @param string $code unique article code
     *
     * @return Article|null
     */
    public function findOneByCode(string $code): ?Article
    {
        try {
            return $this->createQueryBuilder('a')
                ->andWhere('a.code = :code')
                ->setParameter('code', $code)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            //this cannot be reached because of set max results
            //this cannot be reached because of unique index on code
            return null;
        }
    }
}
