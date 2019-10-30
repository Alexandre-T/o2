<?php

namespace App\Manager;

use App\Entity\AskedVat;
use App\Entity\EntityInterface;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * User Manager.
 *
 * @category Manager
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class AskedVatManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Accountant can accept the new Vat of this customer.
     *
     * @param AskedVat $askedVat the customer
     */
    public function acceptVat(AskedVat $askedVat): void
    {
        $customer = $askedVat->getCustomer();
        $askedVat->setStatus(AskedVat::ACCEPTED);
        $customer->setVat($askedVat->getVat());
    }

    /**
     * Customer ask the defaultVat
     *
     * @param User $customer the customer
     */
    public function askDefaultVat(User $customer): void
    {

    }

    /**
     * Customer ask the DOM VAT.
     *
     * @param User $customer the customer
     */
    public function askDomVat(User $customer): void
    {

    }

    /**
     * Customer ask a new VAT.
     *
     * @param User   $customer the customer
     * @param string $vatIntra Customer Intra VAT number
     */
    public function askEuropeVat(User $customer, string $vatIntra): void
    {

    }

    /**
     * Accountant rejectVat of customer
     *
     * @param User $customer the customer
     */
    public function rejectVat(User $customer): void
    {

    }

    /**
     * Return default alias.
     */
    public function getDefaultAlias(): string
    {
        return 'askedVat';
    }

    /**
     * Get the default field for ordering data.
     *
     * @return string
     */
    public function getDefaultSortField(): string
    {
        return 'createdAt';
    }

    /**
     * Main repository getter.
     *
     * @return EntityRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AskedVat::class);
    }

    /**
     * Is this entity deletable?
     *
     * @param EntityInterface $entity entity to test
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool
    {
        return $entity instanceof AskedVat;
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
            ->innerJoin('askedVat.customer', 'customer')
            ->addSelect('customer.name as HIDDEN customers')
            ->addSelect('askedVat.createdAt as HIDDEN createdAt')
            ;
    }
}