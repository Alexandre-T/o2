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

namespace App\Manager;

use App\Entity\EntityInterface;
use App\Entity\Programmation;
use App\Entity\User;
use App\Repository\ProgrammationRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Programmation Manager.
 */
class ProgrammationManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Const for the alias query.
     */
    public const ALIAS = 'programmation';

    /**
     * Return default alias.
     */
    public function getDefaultAlias(): string
    {
        return self::ALIAS;
    }

    /**
     * Get the default field for ordering data.
     *
     * @return string
     */
    public function getDefaultSortField(): string
    {
        return self::ALIAS.'.id';
    }

    /**
     * Return the Query builder needed by the paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->repository->createQueryBuilder(self::ALIAS);
    }

    /**
     * Is this entity deletable?
     *
     * @param EntityInterface $entity the entity to test
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool
    {
        return false;
    }

    /**
     * Paginate bills with criteria on user.
     *
     * @param User   $user      User criteria
     * @param int    $page      number of page
     * @param int    $limit     limit of bills per page
     * @param string $sortField sort field
     * @param string $sortOrder sort order
     *
     * @throws QueryException when criteria is not valid
     *
     * @return PaginationInterface
     */
    public function paginateWithUser(
     User $user,
     int $page,
     int $limit,
     string $sortField,
     string $sortOrder
    ): PaginationInterface {
        $criteria = Criteria::create();
        $expression = $criteria::expr()->eq('customer', $user);
        $criteria->where($expression);

        return $this->paginateWithCriteria(
            $criteria,
            $page,
            $limit,
            $sortField,
            $sortOrder
        );
    }

    /**
     * This method will add the HIDDEN field, the sortable field.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/196
     *
     * @param QueryBuilder $queryBuilder Query builder
     *
     * @return QueryBuilder
     */
    protected function addHiddenField(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->addSelect(self::ALIAS.'.createdAt as HIDDEN createdAt')
        ;
    }

    /**
     * Return the main repository.
     *
     * @return ProgrammationRepository|EntityRepository|ObjectRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Programmation::class);
    }
}
