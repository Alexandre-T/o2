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

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * UserFixtures class.
 *
 * TODO: https://symfonycasts.com/screencast/symfony-security/user-entity#play
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
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array(getenv('APP_ENV'), ['dev', 'test'])) {
            //Admin
            $userAdministrator = new User();
            $userAdministrator
                ->setGivenName('John')
                ->setName('Admin')
                ->setMail('administrator@example.org')
                ->setPlainPassword('administrator')
                ->addRole(User::ROLE_ADMIN)
            ;

            //All
            $userAll = new User();
            $userAll
                ->setCredit(420)
                ->setMail('all@example.org')
                ->setPlainPassword('all')
                ->addRole(User::ROLE_ADMIN)
                ->addRole(User::ROLE_ACCOUNTANT)
                ->addRole(User::ROLE_PROGRAMMER)
                ->setSociety('All power')
                ->setType(User::MORAL)
            ;

            //Reader
            $userAccountant = new User();
            $userAccountant
                ->setCredit(210)
                ->setGivenName('Johanna')
                ->setName('Accountant')
                ->setMail('accountant@example.org')
                ->setPlainPassword('accountant')
                ->addRole(User::ROLE_ACCOUNTANT)
            ;

            //Programmer
            $userProgrammer = new User();
            $userProgrammer
                ->setGivenName('Johan')
                ->setMail('programmer@example.org')
                ->setPlainPassword('programmer')
                ->addRole(User::ROLE_PROGRAMMER)
            ;

            //User
            $userCustomer = new User();
            $userCustomer
                ->setCredit(320)
                ->setGivenName('Johannie')
                ->setName('The Customer')
                ->setMail('customer@example.org')
                ->setPlainPassword('customer')
            ;

            //We add a lot of user
            foreach (range(0, self::CUSTOMERS) as $index) {
                $user = new User();
                $user->setMail("customer-${index}@example.org")
                    ->setCredit($index)
                    ->setGivenName("John${index}")
                    ->setName('Doe')
                    ->setPlainPassword("customer-${index}")
                    ->setSociety("Society ${index}")
                    ->setType(0 === $index % 2)
                ;
                $manager->persist($user);
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
}
