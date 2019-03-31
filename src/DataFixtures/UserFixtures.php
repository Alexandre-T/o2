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

class UserFixtures extends Fixture
{
    /**
     * Quantity of clients to load.
     */
    public const CLIENTS = 30;

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
                ->addRole(User::ROLE_COMPTABLE)
                ->addRole(User::ROLE_PROGRAMMER)
                ->setSociety('All power')
                ->setType(User::MORAL)
            ;

            //Reader
            $userComptable = new User();
            $userComptable
                ->setCredit(210)
                ->setGivenName('Johanna')
                ->setName('Comptable')
                ->setMail('comptable@example.org')
                ->setPlainPassword('comptable')
                ->addRole(User::ROLE_COMPTABLE)
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
            $userClient = new User();
            $userClient
                ->setCredit(320)
                ->setGivenName('Johannie')
                ->setName('The Client')
                ->setMail('user@example.org')
                ->setPlainPassword('user')
            ;

            //We add a lot of user
            foreach (range(0, self::CLIENTS) as $index) {
                $user = new User();
                $user->setMail("client-${index}@example.org")
                    ->setCredit($index)
                    ->setGivenName("John${index}")
                    ->setName('Doe')
                    ->setPlainPassword("client-${index}")
                    ->setSociety("Society ${index}")
                    ->setType(0 === $index % 2)
                ;
                $manager->persist($user);
            }

            //These references are perhaps unused.
            $this->addReference('user_all', $userAll);
            $this->addReference('user_client', $userClient);
            $this->addReference('user_comptable', $userComptable);
            $this->addReference('user_programmer', $userProgrammer);
            $this->addReference('user_admin', $userAdministrator);

            //Persist dev and test data
            $manager->persist($userAll);
            $manager->persist($userClient);
            $manager->persist($userComptable);
            $manager->persist($userProgrammer);
            $manager->persist($userAdministrator);

            $manager->flush();
        }
    }
}
