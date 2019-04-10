<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use App\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Manager Interface.
 */
interface ManagerInterface
{
    /**
     * Default order: ASC.
     */
    const SORT = 'ASC';

    /**
     * Default limit rows: 25.
     */
    const LIMIT = 25;

    /**
     * Each entity stored is countable.
     *
     * @param array $criteria
     *
     * @return int
     */
    public function count(array $criteria = []): int;

    /**
     * Delete entity without verification.
     *
     * @param EntityInterface $entity
     */
    public function delete(EntityInterface $entity): void;

    /**
     * Get the default field for ordering data.
     *
     * @return string
     */
    public function getDefaultSortField(): string;

    /**
     * Is this entity deletable?
     *
     * @param EntityInterface $entity
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool;

    /**
     * Get pagination for a class.
     *
     * @param int    $page
     * @param int    $limit
     * @param string $sort
     *
     * @return PaginationInterface
     */
    public function paginate(int $page = 1, int $limit = self::LIMIT, string $sort = self::SORT): PaginationInterface;

    /**
     * Get pagination with criteria for a class.
     *
     * @param Criteria $criteria
     * @param int      $page
     * @param int      $limit
     * @param string   $sort
     *
     * @return PaginationInterface
     */
    public function paginateWithCriteria(Criteria $criteria, int $page = 1, int $limit = self::LIMIT, string $sort = self::SORT): PaginationInterface;

    /**
     * Save entity.
     *
     * @param EntityInterface $entity
     * @param User            $user
     */
    public function save(EntityInterface $entity): void;
}
