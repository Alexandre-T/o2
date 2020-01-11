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
use DateTimeImmutable;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
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
     * Count pending programmation.
     */
    public function countPending(): int
    {
        return $this->count(['deliveredAt' => null]);
    }

    /**
     * Return default alias.
     */
    public function getDefaultAlias(): string
    {
        return self::ALIAS;
    }

    /**
     * Get the default field for ordering data.
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
     * Set delivery date.
     *
     * @param Programmation $programmation the programmation to update
     */
    public function publish(Programmation $programmation): void
    {
        $programmation->setDeliveredAt(new DateTimeImmutable());
    }

    /**
     * This method will add the HIDDEN field, the sortable field.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/196
     *
     * @param QueryBuilder $queryBuilder Query builder
     */
    protected function addHiddenField(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->addSelect(self::ALIAS.'.createdAt as HIDDEN createdAt')
            ->addSelect(self::ALIAS.'.make as HIDDEN make')
            ->addSelect(self::ALIAS.'.model as HIDDEN model')
            ->addSelect(self::ALIAS.'.deliveredAt as HIDDEN deliveredAt');
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
