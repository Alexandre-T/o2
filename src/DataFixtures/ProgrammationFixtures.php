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

use App\Entity\File;
use App\Entity\Programmation;
use App\Entity\User;
use App\Model\ProgrammationInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

/**
 * Programmation fixtures.
 */
class ProgrammationFixtures extends Fixture implements DependentFixtureInterface
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
            FileFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * Load programmations.
     *
     * @param ObjectManager $manager manager to save data
     *
     * @throws Exception returned by DateTimeImmutable
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array(getenv('APP_ENV'), ['dev', 'test'])) {
            /** @var User $customer */
            $customer = $this->getReference('user_customer');
            foreach (range(1, 10) as $index) {
                /** @var File $file */
                $file = $this->getReference('file'.$index);
                $programmation = new Programmation();
                $programmation->setCustomer($customer);
                $programmation->setCylinderCapacity(3.2);
                $programmation->setGearAutomatic(ProgrammationInterface::GEAR_MANUAL);
                $programmation->setComment('Comment'.$index);
                $programmation->setEdcOff(true);
                $programmation->setOdb(ProgrammationInterface::ODB_BOOT);
                $programmation->setOdometer(30000 + $index);
                $programmation->setOriginalFile($file);
                $programmation->setMake('Make'.$index);
                $programmation->setModel('Model'.$index);
                $programmation->setOriginalFile($file);
                $programmation->setPower(100 + $index);
                $programmation->setProtocol('Protocol'.$index);
                $programmation->setRead(ProgrammationInterface::READ_REAL);
                $programmation->setReaderTool("ReaderTool{$index}");
                $programmation->setSerial("Serial{$index}");
                $programmation->setVersion("Version{$index}");
                $programmation->setYear(2009);
                $programmation->refreshCost();
                $manager->persist($programmation);
            }
        }

        $manager->flush();
    }
}
