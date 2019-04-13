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
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * order Manager.
 */
class OrderManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Const for the alias query.
     */
    public const ALIAS = 'order';

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
        return self::ALIAS.'.label';
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
     * @param EntityInterface|Order $entity the entity to test
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool
    {
        return !$entity->isPaid();
    }

    /**
     * Find the only one non-paid order or create a new one.
     *
     * @param User $user the user criteria
     *
     * @return Order
     */
    public function getNonPaidOrder(User $user): Order
    {
        $order = $this->getMainRepository()->findOneNonPaidByUser($user);

        if (null === $order) {
            $order = new Order();
            $order->setCustomer($user);
        }

        return $order;
    }

    /**
     * Return the main repository.
     *
     * @return EntityRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }
}
