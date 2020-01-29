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

namespace App\Tests\Repository;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Settings repository test.
 *
 * @internal
 * @coversDefaultClass
 */
class SettingsRepositoryTest extends KernelTestCase
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Settings Repository.
     *
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * Setup the repository before each test.
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $this->settingsRepository = $this->entityManager->getRepository(Settings::class);
    }

    /**
     * Close entity manager to avoid memory leaks.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    /**
     * Test the findOneCanceled method.
     */
    public function testFindOneByCode(): void
    {
        $actual = 'bill-country';
        $expected = 'FRANCE';
        $settings = $this->settingsRepository->findOneByCode($actual);
        self::assertNotNull($settings);
        self::assertInstanceOf(Settings::class, $settings);
        self::assertSame($expected, $settings->getValue());

        $actual = 'non-existent';
        $settings = $this->settingsRepository->findOneByCode($actual);
        self::assertNull($settings);
    }
}
