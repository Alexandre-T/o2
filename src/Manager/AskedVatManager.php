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
class AskedVatManager extends AbstractRepositoryManager implements ManagerInterface, VatManagerInterface
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
     * Customer ask the defaultVat.
     *
     * @param User $customer the customer
     */
    public function askDefaultVat(User $customer): AskedVat
    {
        $asked = new AskedVat();
        $asked->setVat((string) VatManagerInterface::DEFAULT_VAT);
        $asked->setCustomer($customer);
        $this->save($asked);

        return $asked;
    }

    /**
     * Customer ask the DOM VAT.
     *
     * @param User   $customer   the customer
     * @param string $postalCode the postal code of customer
     */
    public function askDomVat(User $customer, string $postalCode): AskedVat
    {
        $asked = new AskedVat();
        $asked->setVat((string) VatManagerInterface::DOMTOM_VAT);
        $asked->setCustomer($customer);
        $asked->setCode($postalCode);
        $this->save($asked);

        return $asked;
    }

    /**
     * Customer ask a new VAT.
     *
     * @param User   $customer the customer
     * @param string $vatIntra Customer Intra VAT number
     */
    public function askEuropeVat(User $customer, string $vatIntra): AskedVat
    {
        $asked = new AskedVat();
        $asked->setVat((string) VatManagerInterface::EUROPE_VAT);
        $asked->setCustomer($customer);
        $asked->setCode($vatIntra);
        $this->save($asked);

        return $asked;
    }

    /**
     * Ask Vat.
     *
     * We suppose that values are tested
     *
     * @param User        $customer    the customer asking a new vat
     * @param string      $vat         the new vat
     * @param string|null $explanation the explanation if necessary
     */
    public function askVat(User $customer, string $vat, ?string $explanation): AskedVat
    {
        switch ((float) $vat) {
            case self::EUROPE_VAT:
                return $this->askEuropeVat($customer, $explanation);
            case self::DOMTOM_VAT:
                return $this->askDomVat($customer, $explanation);
            default:
                return $this->askDefaultVat($customer);
        }
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
     */
    public function getDefaultSortField(): string
    {
        return 'createdAt';
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
     * Accountant rejectVat of customer.
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
     * This method will add the HIDDEN field, the sortable field.
     *
     * @see https://github.com/KnpLabs/KnpPaginatorBundle/issues/196
     *
     * @param QueryBuilder $queryBuilder Query builder
     */
    protected function addHiddenField(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->innerJoin('askedVat.customer', 'customer')
            ->addSelect('customer.name as HIDDEN customers')
            ->addSelect('askedVat.createdAt as HIDDEN createdAt')
            ;
    }

    /**
     * Main repository getter.
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AskedVat::class);
    }
}
