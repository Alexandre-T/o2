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
use App\Model\ServiceStatusInterface;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

/**
 * Settings fixtures.
 */
class SettingsFixtures extends Fixture
{
    /**
     * Load settings.
     *
     * @param ObjectManager $manager manager to save data
     *
     * @throws Exception cannot happened because DateTimeImmutable constructor is used without param
     */
    public function load(ObjectManager $manager): void
    {
        //Address for bill.
        $manager->persist($this->createSettings('bill-name', 'Society example'));

        //Address for bill.
        $manager->persist($this->createSettings('bill-street-address', '42 boulevard Jean Jaurès'));

        //Complement for bill.
        $manager->persist($this->createSettings('bill-complement', 'CEDEX 33000'));

        //Postal code for bill.
        $manager->persist($this->createSettings('bill-postal-code', '33160'));

        //Locality for bill.
        $manager->persist($this->createSettings('bill-locality', 'MÉRIGNAC'));

        //Country for bill.
        $manager->persist($this->createSettings('bill-country', 'FRANCE'));

        //Siret for bill.
        $manager->persist($this->createSettings('bill-siret', 'SIRET: 699-42-996'));

        //Status for bill.
        $manager->persist($this->createSettings('bill-status', "SARL au capital de 42,00\u{a0}€"));

        //VAT percent for bill.
        $manager->persist($this->createSettings('bill-vat-percent', "20,00\u{a0}%"));

        //Telephone for bill.
        $manager->persist($this->createSettings('bill-telephone', '06-06-06-06-06'));

        //TVA-Number for bill.
        $manager->persist($this->createSettings('bill-vat-number', 'XXX-333-YYY'));

        //Telephone for bill.
        $manager->persist($this->createSettings('bill-url', 'www.example.org'));

        //Mail sender.
        $manager->persist($this->createSettings('mail-sender', 'sender@example.org'));

        //Mail accountant.
        $manager->persist($this->createSettings('mail-accountant', 'accountant@example.org'));

        //Mail programmer.
        $manager->persist($this->createSettings('mail-programmer', 'programmer@example.org'));

        //RCS for legacy rcs.
        $manager->persist($this->createSettings('legacy-rcs', 'RCS-1234-5678'));

        //RCS for legacy publication director.
        $manager->persist($this->createSettings('legacy-publication', 'John Doe'));

        //Host for hostname.
        $manager->persist($this->createSettings('host-name', 'HOST'));

        //Host for host status society form.
        $manager->persist($this->createSettings('host-form', 'HOST form'));

        //Host for host address.
        $manager->persist($this->createSettings('host-address', 'HOST address'));

        //Host for host tel.
        $manager->persist($this->createSettings('host-tel', 'HOST tel'));

        //Status of the service.
        $manager->persist($this->createSettings('service-status', ServiceStatusInterface::CLOSE));

        //Service is close until...
        $manager->persist($this->createSettings('service-until', new DateTimeImmutable()));

        $manager->flush();
    }

    /**
     * Setting factory.
     *
     * @param string $code      the settings code
     * @param string $value     the settings value
     * @param bool   $updatable set to true if administrator can change value
     *
     * @return Settings
     */
    private function createSettings(string $code, $value, $updatable = true): Settings
    {
        $settings = new Settings();
        $settings->setCode($code);
        $settings->setValue($value);
        $settings->setUpdatable($updatable);

        return $settings;
    }
}
