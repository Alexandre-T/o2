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

        //Siret for bill.
        $settings = new Settings();
        $settings->setCode('bill-siret');
        $settings->setValue('SIRET: 699-42-996');
        $manager->persist($settings);

        //Status for bill.
        $settings = new Settings();
        $settings->setCode('bill-status');
        $settings->setValue("SARL au capital de 42,00\u{a0}€");
        $manager->persist($settings);

        //VAT percent for bill.
        $settings = new Settings();
        $settings->setCode('bill-vat-percent');
        $settings->setValue("20,00\u{a0}%");
        $manager->persist($settings);

        //Telephone for bill.
        $settings = new Settings();
        $settings->setCode('bill-telephone');
        $settings->setValue('06-06-06-06-06');
        $manager->persist($settings);

        //TVA-Number for bill.
        $settings = new Settings();
        $settings->setCode('bill-vat-number');
        $settings->setValue('XXX-333-YYY');
        $manager->persist($settings);

        //Telephone for bill.
        $settings = new Settings();
        $settings->setCode('bill-url');
        $settings->setValue('www.example.org');
        $manager->persist($settings);

        //Mail sender.
        $settings = new Settings();
        $settings->setCode('mail-sender');
        $settings->setValue('sender@example.org');
        $manager->persist($settings);

        //Mail accountant.
        $settings = new Settings();
        $settings->setCode('mail-accountant');
        $settings->setValue('accountant@example.org');
        $manager->persist($settings);

        //Mail programmer.
        $settings = new Settings();
        $settings->setCode('mail-programmer');
        $settings->setValue('programmer@example.org');
        $manager->persist($settings);

        //RCS for legacy mentions.
        $settings = new Settings();
        $settings->setCode('legacy-rcs');
        $settings->setValue('RCS-1234-5678');
        $manager->persist($settings);

        //RCS for legacy mentions.
        $settings = new Settings();
        $settings->setCode('legacy-publication');
        $settings->setValue('John Doe');
        $manager->persist($settings);

        //Host for legacy mentions.
        $settings = new Settings();
        $settings->setCode('host-name');
        $settings->setValue('HOST');
        $manager->persist($settings);

        //Host for legacy mentions.
        $settings = new Settings();
        $settings->setCode('host-form');
        $settings->setValue('HOST form');
        $manager->persist($settings);

        //Host for legacy mentions.
        $settings = new Settings();
        $settings->setCode('host-address');
        $settings->setValue('HOST address');
        $manager->persist($settings);

        //Host for legacy mentions.
        $settings = new Settings();
        $settings->setCode('host-tel');
        $settings->setValue('HOST tel');
        $manager->persist($settings);

        $manager->flush();
    }
}
