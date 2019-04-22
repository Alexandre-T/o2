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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

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
     * @param EntityInterface|Bill $entity
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool
    {
        return false;
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
            if (!$bill->isCanceled) {
                return $bill;
            }
        }

        return BillFactory::create($order, $user);
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
