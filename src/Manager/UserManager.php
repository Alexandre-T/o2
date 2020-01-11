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

use Alexandre\EvcBundle\Exception\EvcException;
use Alexandre\EvcBundle\Service\EvcServiceInterface;
use App\Entity\EntityInterface;
use App\Entity\OlsxInterface;
use App\Entity\Programmation;
use App\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * User Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class UserManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Const for the alias query.
     */
    public const ALIAS = 'user';

    /**
     * Activate user, so he will have access to OLSX service.
     *
     * @param User $customer the customer which will have access to OLSX service
     */
    public function activateOlsx(User $customer): bool
    {
        if (null === $customer->getOlsxIdentifier()) {
            return false;
        }

        $customer->setRegistered();
        $customer->addRole('ROLE_OLSX');
        $this->save($customer);

        return true;
    }

    /**
     * Convert a customer to a personal customer of current reseller.
     *
     * @param User                $customer   the customer to convert
     * @param EvcServiceInterface $evcService the evc Service
     *
     * @throws EvcException when an occurred on EVC Service of when customer does not exists
     */
    public function convertAsPersonal(User $customer, EvcServiceInterface $evcService): void
    {
        $evcService->createPersonalCustomer($customer->getOlsxIdentifier());
    }

    /**
     * Remove credits to user by programmation cost.
     *
     * @param Programmation $programmation programmation to debit
     */
    public function debit(Programmation $programmation): void
    {
        $programmation->refreshCost();
        $user = $programmation->getCustomer();
        $user->setCredit($user->getCredit() - $programmation->getCredit());
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
     * @param EntityInterface|User $entity the entity to test
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool
    {
        return empty($entity->getBills()->count());
    }

    /**
     * Return a pagination of users which are registering to OLSX service.
     *
     * @param int    $page      page needed
     * @param int    $limit     limit of users per page
     * @param string $sortField sort field
     * @param string $sortOrder sort order
     *
     * @throws QueryException on error with query
     *
     * @return PaginationInterface
     */
    public function paginateRegisteringUsers(int $page, int $limit, string $sortField, string $sortOrder)
    {
        //Construct criteria
        $criteria = Criteria::create();
        $expression = Criteria::expr()->andX(
            Criteria::expr()->eq('olsxStatus', OlsxInterface::REGISTERING),
            Criteria::expr()->gt('olsxIdentifier', 0)
        );
        $criteria->where($expression);

        //construct hidden field for sort order
        $hiddenFields = [
            'user.name as HIDDEN customers',
            'user.olsxIdentifier as HIDDEN identifiers',
        ];

        return $this->paginateWithCriteria($criteria, $page, $limit, $sortField, $sortOrder, $hiddenFields);
    }

    /**
     * Unactivate an Olsx customer.
     *
     * @param User $customer the customer to remove
     */
    public function unactivateOlsx(User $customer): bool
    {
        $customer->setRegistering();
        $customer->removeRole('ROLE_OLSX');
        $this->save($customer);

        return true;
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
            ->addSelect('user.credit as HIDDEN credit')
            ->addSelect('user.mail as HIDDEN mail')
            ->addSelect('user.name as HIDDEN username');
    }

    /**
     * Return the main repository.
     *
     * @return EntityRepository|ObjectRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(User::class);
    }
}
