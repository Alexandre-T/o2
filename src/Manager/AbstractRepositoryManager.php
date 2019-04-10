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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException as QueryExceptionAlias;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Abstract manager class.
 *
 * Provides useful function for main repository.
 */
abstract class AbstractRepositoryManager implements ManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var LogEntryRepository
     */
    protected $logRepository;

    /**
     * AbstractRepositoryManager constructor.
     *
     * @param EntityManagerInterface $entityManager entity manager provided by dependency injection
     * @param PaginatorInterface     $paginator     paginator provided by dependency injection
     */
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->logRepository = $entityManager->getRepository(LogEntry::class);
        $this->paginator = $paginator;
        $this->repository = $this->getMainRepository();
    }

    /**
     * Return the number of current entities registered in database.
     *
     * @param array $criteria filter criteria
     *
     * @return int
     */
    public function count(array $criteria = []): int
    {
        return $this->repository->count($criteria);
    }

    /**
     * Delete entity without verification.
     *
     * @param EntityInterface $entity entity to delete
     */
    public function delete(EntityInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * Return default alias.
     */
    abstract public function getDefaultAlias(): string;

    /**
     * Get the default field for ordering data.
     *
     * @return string
     */
    abstract public function getDefaultSortField(): string;

    /**
     * Return the Query builder needed by the paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->repository->createQueryBuilder($this->getDefaultAlias());
    }

    /**
     * Get pagination for a class.
     *
     * @param int         $page      page to display
     * @param int         $limit     maximum entity per page
     * @param string|null $sortField sort field
     * @param string      $sortOrder sort order
     *
     * @return PaginationInterface
     */
    public function paginate(
     int $page = 1,
     int $limit = self::LIMIT,
     string $sortField = null,
     $sortOrder = self::SORT
    ): PaginationInterface {
        $queryBuilder = $this->repository->createQueryBuilder($this->getDefaultAlias());

        $queryBuilder = $this->addHiddenField($queryBuilder);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $limit,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => $sortField,
                PaginatorInterface::DEFAULT_SORT_DIRECTION => $sortOrder,
            ]
        );
    }

    /**
     * Get pagination with criteria for a class.
     *
     * @param Criteria    $criteria  filter criteria
     * @param int         $page      page to display
     * @param int         $limit     number of entity to display
     * @param string|null $sortField sort field
     * @param string      $sortOrder sort order
     *
     * @throws QueryExceptionAlias when criteria are not valid
     *
     * @return PaginationInterface
     */
    public function paginateWithCriteria(
     Criteria $criteria,
     int $page = 1,
     int $limit = self::LIMIT,
     string $sortField = null,
     $sortOrder = self::SORT
    ): PaginationInterface {
        $queryBuilder = $this->repository->createQueryBuilder($this->getDefaultAlias());
        $queryBuilder->addCriteria($criteria);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $limit,
            [
                'defaultSortFieldName' => $sortField,
                'defaultSortDirection' => 'ASC' == $sortOrder ? $sortOrder : 'DESC',
            ]
        );
    }

    /**
     * Retrieve logs of the services.
     *
     * @param EntityInterface $entity entity to retrieve logs
     *
     * @return LogEntry[]
     */
    public function retrieveLogs(EntityInterface $entity): array
    {
        if (empty($entity)) {
            return [];
        }

        return $this->logRepository->getLogEntries($entity);
    }

    /**
     * Save new or modified entity.
     *
     * @param EntityInterface $entity entity to save
     */
    public function save(EntityInterface $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Main repository getter.
     *
     * @return EntityRepository
     */
    abstract protected function getMainRepository(): EntityRepository;

    /**
     * This method will add the HIDDEN field, the sortable field.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/196
     *
     * @param QueryBuilder $queryBuilder the query builder to update
     *
     * @return QueryBuilder
     */
    abstract protected function addHiddenField(QueryBuilder $queryBuilder): QueryBuilder;
}