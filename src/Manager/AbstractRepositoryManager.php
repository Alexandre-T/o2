<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use App\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
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
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface     $paginator
     */
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->logRepository = $entityManager->getRepository(LogEntry::class);
        $this->paginator = $paginator;
        $this->repository = $this->getMainRepository();
    }

    /**
     * @return EntityRepository
     */
    abstract protected function getMainRepository(): EntityRepository;

    /**
     * Return the number of current entities registered in database.
     *
     * @param array $criteria
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
     * @param EntityInterface $entity
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
     * @param int         $page
     * @param int         $limit
     * @param string|null $sortField
     * @param string      $sortOrder
     *
     * @return PaginationInterface
     */
    public function paginate(int $page = 1, int $limit = self::LIMIT, string $sortField = null, $sortOrder = self::SORT): PaginationInterface
    {
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
     * @param Criteria    $criteria
     * @param int         $page
     * @param int         $limit
     * @param string|null $sortField
     * @param string      $sortOrder
     *
     * @return PaginationInterface
     *
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function paginateWithCriteria(Criteria $criteria, int $page = 1, int $limit = self::LIMIT, string $sortField = null, $sortOrder = self::SORT): PaginationInterface
    {
        $queryBuilder = $this->repository->createQueryBuilder($this->getDefaultAlias());
        $queryBuilder->addCriteria($criteria);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $limit,
            ['defaultSortFieldName' => $this->getDefaultSortField(), 'defaultSortDirection' => 'ASC' == $sortOrder ? $sortOrder : 'DESC']
        );
    }

    /**
     * Retrieve logs of the services.
     *
     * @param EntityInterface $entity
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
     * Save new or modified User.
     *
     * @param EntityInterface $entity
     * @param User            $user
     */
    public function save(EntityInterface $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * This method will add the HIDDEN field, the sortable field.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/196
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    abstract protected function addHiddenField(QueryBuilder $queryBuilder): QueryBuilder;
}
