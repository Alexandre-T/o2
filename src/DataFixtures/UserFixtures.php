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

use App\Entity\PersonInterface;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

/**
 * UserFixtures class.
 */
class UserFixtures extends Fixture
{
    /**
     * Quantity of customers to load.
     */
    public const CUSTOMERS = 30;

    /**
     * Load users.
     *
     * @param ObjectManager $manager manager to save data
     *
     * @throws Exception emits Exception on error during DateTimeImmutable initialization
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array(getenv('APP_ENV'), ['dev', 'test'])) {
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

            //User
            $userCustomer = $this->createUser('The customer', 'customer');
            $userCustomer
                ->setCredit(320)
                ->setGivenName('Johannie')
            ;

            $now = new DateTimeImmutable();

            //We add a lot of user
            foreach (range(0, self::CUSTOMERS) as $index) {
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
                $manager->persist($user);
                $this->addReference("user_customer-{$index}", $user);
            }

            //These references are perhaps unused.
            $this->addReference('user_all', $userAll);
            $this->addReference('user_customer', $userCustomer);
            $this->addReference('user_accountant', $userAccountant);
            $this->addReference('user_programmer', $userProgrammer);
            $this->addReference('user_admin', $userAdministrator);

            //Persist dev and test data
            $manager->persist($userAll);
            $manager->persist($userCustomer);
            $manager->persist($userAccountant);
            $manager->persist($userProgrammer);
            $manager->persist($userAdministrator);

            $manager->flush();
        }
    }

    /**
     * Create a testing administrator.
     *
     * @param string $label The user label
     * @param string $code  A code to connect
     *
     * @return User
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
     *
     * @return User
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
     * Create a testing user.
     *
     * @param string $label The user label
     * @param string $code  A code to connect
     *
     * @return User
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
        $user
            ->setPostalCode('33000')
            ->setStreetAddress('rue du boulevard')
            ->setCountry('FR')
            ->setLocality('locality')
        ;

        return $user;
    }
}
