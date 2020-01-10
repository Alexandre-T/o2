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

namespace App\DataFixtures;

use App\Entity\AskedVat;
use App\Entity\User;
use App\Manager\VatManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * AskedVat fixtures.
 */
class AskedVatFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    /**
     * Load articles.
     *
     * @param ObjectManager $manager manager to save data
     */
    public function load(ObjectManager $manager): void
    {
        //Create undecided data
        $manager->persist($this->createEntity(
            'user_customer-1',
            VatManagerInterface::EUROPE_VAT,
            'TVA-INTRA-0001'
        ));

        $manager->persist($this->createEntity(
            'user_customer-4',
            VatManagerInterface::EUROPE_VAT
        ));

        $manager->persist($this->createEntity(
            'user_customer-2',
            VatManagerInterface::DOMTOM_VAT,
            '33680'
        ));

        $manager->persist($this->createEntity(
            'user_customer-6',
            VatManagerInterface::DEFAULT_VAT
        ));

        //Get accountant reference
        /** @var User $accountant */
        $accountant = $this->getReference('user_accountant');

        //Creating some rejected vat
        $manager->persist($this->createEntity(
            'user_customer-14',
            VatManagerInterface::EUROPE_VAT,
            null,
            $accountant,
            AskedVat::REJECTED
        ));

        $manager->persist($this->createEntity(
            'user_customer-15',
            VatManagerInterface::DEFAULT_VAT,
            '97100',
            $accountant,
            AskedVat::REJECTED
        ));

        $manager->persist($this->createEntity(
            'user_customer-16',
            VatManagerInterface::DOMTOM_VAT,
            '33680',
            $accountant,
            AskedVat::REJECTED
        ));

        //Create some accepted vat
        $manager->persist($this->createEntity(
            'user_customer-24',
            VatManagerInterface::EUROPE_VAT,
            'TVA-NUMBER-111',
            $accountant,
            AskedVat::ACCEPTED
        ));

        $manager->persist($this->createEntity(
            'user_customer-25',
            VatManagerInterface::DEFAULT_VAT,
            '33680',
            $accountant,
            AskedVat::ACCEPTED
        ));

        $manager->persist($this->createEntity(
            'user_customer-26',
            VatManagerInterface::DOMTOM_VAT,
            '97200',
            $accountant,
            AskedVat::ACCEPTED
        ));

        $manager->flush();
    }

    /**
     * Create an entity.
     *
     * @param string      $customer   the customer reference which is asking a new vat
     * @param float       $vat        the vat asked
     * @param string|null $vatNumber  the vat number or the postal code
     * @param User|null   $accountant the accountant deciding
     * @param int|null    $decision   the decision
     */
    private function createEntity(
        string $customer,
        float $vat,
        ?string $vatNumber = null,
        ?User $accountant = null,
        ?int $decision = null
    ): AskedVat {
        $asked = new AskedVat();
        /** @var User $user */
        $user = $this->getReference($customer);
        $asked->setCustomer($user);
        $asked->setVat((string) $vat);
        if (null !== $vatNumber) {
            $asked->setCode($vatNumber);
        }

        if (null !== $accountant && null !== $decision) {
            $asked->setAccountant($accountant);
            $asked->setStatus($decision);
        }

        return $asked;
    }
}
