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
    /**
     * AskedVatRepository constructor.
     *
     * @param ManagerRegistry $registry provided by injection dependencies
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AskedVat::class);
    }
}
