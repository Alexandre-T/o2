<?php

namespace App\Manager;

use App\Entity\AskedVat;
use App\Entity\EntityInterface;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Exception;

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
     * @param AskedVat $askedVat   the customer
     * @param User     $accountant the accountant
     */
    public function acceptVat(AskedVat $askedVat, User $accountant): void
    {
        $customer = $askedVat->getCustomer();
        $askedVat->setStatus(AskedVat::ACCEPTED);
        $askedVat->setAccountant($accountant);
        $customer->setVat($askedVat->getVat());
        $customer->setBillIndication($askedVat->getCode());

        try {
            $this->entityManager->beginTransaction();
            $this->save($customer);
            $this->save($askedVat);
            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
        }
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
     * @param AskedVat $askedVat   asked vat
     * @param User     $accountant accountant
     */
    public function rejectVat(AskedVat $askedVat, User $accountant): void
    {
        $askedVat->setStatus(AskedVat::REJECTED);
        $askedVat->setAccountant($accountant);

        $this->save($askedVat);
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