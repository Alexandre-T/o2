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

use App\Entity\LanguageInterface;
use App\Entity\PersonInterface;
use App\Entity\User;
use App\Manager\VatManagerInterface;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserFixtures class.
 */
class UserFixtures extends Fixture
{
    /**
     * Quantity of customers to load.
     */
    public const CUSTOMERS = 30;
    public const OLSX = 6;

    /**
     * Load users.
     *
     * @param ObjectManager $manager manager to save data
     *
     * @throws Exception emits Exception on error during DateTimeImmutable initialization
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array($_ENV['APP_ENV'], ['dev', 'test'], true)) {
            //Admin
            $userAdministrator = $this->createAdmin('Admin', 'administrator');

            //All
            $userAll = $this->createAll('All Power', 'all');
            $userAll
                ->setCredit(420)
                ->setType(PersonInterface::PHYSIC)
            ;

            //All
            $societyAll = $this->createAll('Big brother', 'big');
            $societyAll
                ->setCredit(420)
                ->setType(PersonInterface::PHYSIC)
            ;

            //Accountant
            $userAccountant = $this->createUser('Accountant', 'accountant');
            $userAccountant
                ->setCredit(210)
                ->addRole(User::ROLE_ACCOUNTANT)
            ;

            //Programmer
            $userProgrammer = $this->createUser('Programmer', 'programmer');
            $userProgrammer
                ->addRole(User::ROLE_PROGRAMMER)
            ;

            //Olsx user
            foreach (range(1, self::OLSX + 1) as $index) {
                $userOlsx = $this->createUser("OLSX-{$index}", "olsx{$index}");
                $userOlsx
                    ->setOlsxIdentifier(11111 * $index)
                    ->setRegistered()
                    ->addRole(User::ROLE_OLSX)
                ;
            }

            //Olsx user
            foreach (range(1, self::OLSX + 1) as $index) {
                $userOlsx = $this->createOlsx($index);
                $manager->persist($userOlsx);
                $this->addReference("user_olsx-{$index}", $userOlsx);

                $registeringOlsx = $this->createRegisteringOlsx($index);
                $manager->persist($registeringOlsx);
                $this->addReference("user_registering-{$index}", $registeringOlsx);
            }

            //User
            $userCustomer = $this->createUser('The customer', 'customer');
            $userCustomer
                ->setCredit(320)
                ->setGivenName('Johannie')
            ;

            //We add a lot of user
            foreach (range(0, self::CUSTOMERS) as $index) {
                $user = $this->createCustomer($index);
                $manager->persist($user);
                $this->addReference("user_customer-{$index}", $user);
            }

            //These references are perhaps unused.
            $this->addReference('user_accountant', $userAccountant);
            $this->addReference('user_admin', $userAdministrator);
            $this->addReference('user_all', $userAll);
            $this->addReference('user_customer', $userCustomer);
            $this->addReference('user_olsx', $userOlsx);
            $this->addReference('user_programmer', $userProgrammer);

            //Persist dev and test data
            $manager->persist($userAccountant);
            $manager->persist($userAdministrator);
            $manager->persist($userAll);
            $manager->persist($userCustomer);
            $manager->persist($userProgrammer);

            $manager->flush();
        }
    }

    /**
     * Create a testing administrator.
     *
     * @param string $label The user label
     * @param string $code  A code to connect
     */
    private function createAdmin(string $label, string $code): User
    {
        $admin = $this->createUser($label, $code);
        $admin->addRole(User::ROLE_ADMIN);

        return $admin;
    }

    /**
     * Create a testing user with all privileges.
     *
     * @param string $label The user label
     * @param string $code  A code to connect
     */
    private function createAll(string $label, string $code): User
    {
        $all = $this->createUser($label, $code);
        $all
            ->addRole(User::ROLE_ADMIN)
            ->addRole(User::ROLE_ACCOUNTANT)
            ->addRole(User::ROLE_PROGRAMMER)
        ;

        return $all;
    }

    /**
     * Create a standard customer.
     *
     * @param int $index the index to name customer
     *
     * @throws Exception this should not happen because DateTimeInterface is used without argument
     */
    private function createCustomer($index): UserInterface
    {
        $now = new DateTimeImmutable();

        $user = $this->createUser("Customer {$index}", "customer-{$index}");
        $user
            ->setResettingToken("resetToken{$index}")
            ->setResettingAt($now)
            ->setCredit($index)
            ->setGivenName("John{$index}")
            ->setName('Doe')
            ->setSociety("Society {$index}")
            ->setType(0 === $index % 2)
        ;
        if (0 === $index % 8) {
            $user->setVat((string) VatManagerInterface::DOMTOM_VAT);
            $user->setPostalCode('97200');
            $user->setLocality('Saint-Denis');
            $user->setBillIndication('97200');
        }

        if (0 === $index % 10) {
            $user->setVat((string) VatManagerInterface::EUROPE_VAT);
            $user->setCountry('DE');
            $user->setLocality('Berlin');
            $user->setVatNumber('TVA-BERLIN-CODE');
            $user->setBillIndication('TVA-BERLIN-CODE');
        }

        return $user;
    }

    /**
     * Create an OLSX user.
     *
     * @param int $index the OLSX user index
     */
    private function createOlsx(int $index): User
    {
        $userOlsx = $this->createUser("OLSX-{$index}", "olsx-{$index}");
        $userOlsx
            ->setOlsxIdentifier(11111 * $index)
            ->setRegistered()
            ->addRole(User::ROLE_OLSX)
        ;

        return $userOlsx;
    }

    /**
     * Create a user which is registering.
     *
     * @param int $index the OLSX user index
     */
    private function createRegisteringOlsx($index)
    {
        $userOlsx = $this->createUser("OLSX-registering-{$index}", "registering{$index}");
        $userOlsx
            ->setOlsxIdentifier(11111 * $index)
            ->setRegistering()
        ;

        return $userOlsx;
    }

    /**
     * Create a testing user.
     *
     * @param string $label The user label
     * @param string $code  A code to connect
     */
    private function createUser(string $label, string $code): User
    {
        $user = new User();
        $user
            ->setMail("{$code}@example.org")
            ->setPlainPassword($code)
            ->setGivenName('John')
            ->setName($label)
            ->setType(PersonInterface::PHYSIC)
        ;

        $user->setLanguage(LanguageInterface::FRENCH);

        $user
            ->setPostalCode('33000')
            ->setStreetAddress('rue du boulevard')
            ->setCountry('FR')
            ->setLocality('locality')
        ;

        return $user;
    }
}
