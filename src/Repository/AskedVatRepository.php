<?php

namespace App\Repository;

use App\Entity\AskedVat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AskedVat|null find($id, $lockMode = null, $lockVersion = null)
 * @method AskedVat|null findOneBy(array $criteria, array $orderBy = null)
 * @method AskedVat[]    findAll()
 * @method AskedVat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AskedVatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AskedVat::class);
    }

    // /**
    //  * @return AskedVat[] Returns an array of AskedVat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AskedVat
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
