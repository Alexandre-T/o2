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
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use ReflectionClass;
use ReflectionException;

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
        if (in_array(getenv('APP_ENV'), ['dev', 'test'], true)) {
            foreach (range(1, 40) as $index) {
                /** @var File $file */
                $programmation = $this->createProgrammation($index);
                if (0 === $index % 4) {
                    $this->close($programmation, $index);
                }

                if (0 === $index % 10) {
                    $this->obsolete($programmation, $index);
                }

                $manager->persist($programmation);
            }
        }

        $manager->flush();
    }

    /**
     * The obsolete programmation.
     *
     * @param Programmation $programmation the programmation to set obsolete
     * @param int           $index         the index of programmation to find a file
     *
     * @throws Exception this should NOT happen because I use DateTimeImmutable without constructor
     */
    private function close(Programmation $programmation, int $index): void
    {
        /** @var File $file */
        $file = $this->getReference('file'.$index);
        $programmation->setDeliveredAt(new DateTimeImmutable());
        $programmation->setFinalFile($file);
        $programmation->setEdcStopped(true);
        $programmation->setEgrStopped(true);
        $programmation->setEthanolDone(false);
        $programmation->setFapStopped(true);
        $programmation->setStageOneDone(false);
    }

    /**
     * Create a programmation.
     *
     * @param int $index index is used to complete data
     */
    private function createProgrammation($index): Programmation
    {
        /** @var File $file */
        $file = $this->getReference('file'.$index);
        /** @var User $customer */
        $customer = $this->getReference('user_customer');
        $programmation = new Programmation();
        $programmation->setCustomer($customer);
        $programmation->setCylinderCapacity('3.2');
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

        return $programmation;
    }

    /**
     * The obsolete programmation.
     *
     * @param Programmation $programmation the programmation to set obsolete
     * @param int|string    $index         index is used to get file reference
     *
     * @throws ReflectionException when an error occurred with setCreatedAt function
     */
    private function obsolete(Programmation $programmation, $index): void
    {
        $now = new DateTimeImmutable('now');
        $oldDate = $now->sub(new DateInterval('P2M'));
        /** @var File $file */
        $file = $this->getReference('file'.$index);
        $programmation->setDeliveredAt($oldDate);
        $programmation->setFinalFile($file);
        $programmation->setEdcStopped(true);
        $programmation->setEgrStopped(true);
        $programmation->setEthanolDone(false);
        $programmation->setFapStopped(true);
        $programmation->setStageOneDone(false);
        $this->setCreatedAt($programmation, $oldDate);
    }

    /**
     * Update creation date.
     *
     * @param Programmation     $programmation the programmation to update
     * @param DateTimeInterface $date          the new date
     *
     * @throws ReflectionException when programmation does not have a createdAt property (so never)
     */
    private function setCreatedAt(Programmation $programmation, DateTimeInterface $date): void
    {
        $reflection = new ReflectionClass($programmation);
        $property = $reflection->getProperty('createdAt');
        $property->setAccessible(true);
        $property->setValue($programmation, $date);
    }
}
