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

use App\Entity\OrderedArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Ordered article repository.
 *
 * @method OrderedArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderedArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderedArticle[]    findAll()
 * @method OrderedArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderedArticleRepository extends ServiceEntityRepository
{
    /**
     * OrderedArticleRepository constructor.
     *
     * @param RegistryInterface $registry injected by dependency injection
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, OrderedArticle::class);
    }
}
