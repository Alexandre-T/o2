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

use App\Entity\Bill;
use App\Entity\EntityInterface;
use App\Entity\Order;
use App\Entity\User;
use App\Factory\BillFactory;
use App\Repository\BillRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Bill Manager.
 */
class BillManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Const for the alias query.
     */
    public const ALIAS = 'bill';

    /**
     * Return default alias.
     */
    public function getDefaultAlias(): string
    {
        return self::ALIAS;
    }

    /**
     * Get the default field for billing data.
     *
     * @return string
     */
    public function getDefaultSortField(): string
    {
        return self::ALIAS.'.number';
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
     * Bill are never deletable.
     *
     * @param EntityInterface|Bill $entity entity to test
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
     * Create a bill only if there is no non-canceled bill.
     *
     * @param Order $order Referenced order
     * @param User  $user  Referenced user
     *
     * @return Bill
     */
    public function retrieveOrCreateBill(Order $order, User $user): Bill
    {
        $bills = $this->getMainRepository()->findByOrder($order);

        foreach ($bills as $bill) {
            if (!$bill->isCanceled()) {
                return $bill;
            }
        }

        return BillFactory::create($order, $user);
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
            ->innerJoin('bill.customer', 'customer')
            ->addSelect('bill.number as HIDDEN number')
            ->addSelect('customer.name as HIDDEN customers')
            ->addSelect('bill.price as HIDDEN amount')
            ;
    }

    /**
     * Return the main repository.
     *
     * @return EntityRepository|BillRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Bill::class);
    }
}
