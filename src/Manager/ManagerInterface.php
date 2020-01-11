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
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Manager Interface.
 */
interface ManagerInterface
{
    /**
     * Default limit rows: 25.
     */
    public const LIMIT = 25;

    /**
     * Default order: ASC.
     */
    public const SORT = 'ASC';

    /**
     * Each entity stored is countable.
     *
     * @param array $criteria filter criteria
     */
    public function count(array $criteria = []): int;

    /**
     * Delete entity without verification.
     *
     * @param EntityInterface $entity entity to delete
     */
    public function delete(EntityInterface $entity): void;

    /**
     * Get the default field for ordering data.
     */
    public function getDefaultSortField(): string;

    /**
     * Is this entity deletable?
     *
     * @param EntityInterface $entity entity to test
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool;

    /**
     * Get pagination for a class.
     *
     * @param int    $page  current page
     * @param int    $limit limit of each page
     * @param string $sort  sort order
     */
    public function paginate(int $page = 1, int $limit = self::LIMIT, string $sort = self::SORT): PaginationInterface;

    /**
     * Get pagination with criteria for a class.
     *
     * @param Criteria $criteria filter criteria
     * @param int      $page     current page
     * @param int      $limit    max elements
     * @param string   $sort     sort order
     */
    public function paginateWithCriteria(
        Criteria $criteria,
        int $page = 1,
        int $limit = self::LIMIT,
        string $sort = self::SORT
    ): PaginationInterface;

    /**
     * Save entity.
     *
     * @param EntityInterface $entity entity to save / persist
     */
    public function save(EntityInterface $entity): void;
}
