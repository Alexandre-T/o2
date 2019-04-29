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

use App\Entity\Programmation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Programmation repository.
 *
 * @method Programmation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programmation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programmation[]    findAll()
 * @method Programmation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammationRepository extends ServiceEntityRepository
{
    /**
     * Programmation repository constructor.
     *
     * @param RegistryInterface $registry provided by injection dependency
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Programmation::class);
    }
}
