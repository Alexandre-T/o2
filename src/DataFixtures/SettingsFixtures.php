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

use App\Entity\Settings;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Settings fixtures.
 */
class SettingsFixtures extends Fixture
{
    /**
     * Load settings.
     *
     * @param ObjectManager $manager manager to save data
     */
    public function load(ObjectManager $manager): void
    {
        //Address for bill.
        $settings = new Settings();
        $settings->setCode('bill-name');
        $settings->setValue('Society example');
        $manager->persist($settings);

        //Address for bill.
        $settings = new Settings();
        $settings->setCode('bill-street-address');
        $settings->setValue('42 boulevard Jean Jaurès');
        $manager->persist($settings);

        //Complement for bill.
        $settings = new Settings();
        $settings->setCode('bill-complement');
        $settings->setValue('CEDEX 33000');
        $manager->persist($settings);

        //Postal code for bill.
        $settings = new Settings();
        $settings->setCode('bill-postal-code');
        $settings->setValue('33160');
        $manager->persist($settings);

        //Locality for bill.
        $settings = new Settings();
        $settings->setCode('bill-locality');
        $settings->setValue('MÉRIGNAC');
        $manager->persist($settings);

        //Country for bill.
        $settings = new Settings();
        $settings->setCode('bill-country');
        $settings->setValue('FRANCE');
        $manager->persist($settings);

        //TVA-Number for bill.
        $settings = new Settings();
        $settings->setCode('bill-vat-number');
        $settings->setValue('XXX-333-YYY');
        $manager->persist($settings);

        $manager->flush();
    }
}
