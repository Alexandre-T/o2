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

use App\Exception\SettingsException;
use App\Manager\SettingsManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Settings repository test.
 *
 * @internal
 * @coversDefaultClass
 */
class SettingsManagerTest extends KernelTestCase
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Settings manager.
     *
     * @var SettingsManager
     */
    private $settingsManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $paginator = self::createMock(PaginatorInterface::class);
        $this->settingsManager = new SettingsManager($this->entityManager, $paginator);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    /**
     * Test the getValue method.
     *
     * @throws SettingsException this should not happen
     */
    public function testGetValue(): void
    {
        $actual = 'bill-country';
        $expected = 'FRANCE';
        self::assertEquals($expected, $this->settingsManager->getValue($actual));
    }

    /**
     * Test the GetValue method with a non-existent code.
     *
     * @throws SettingsException this one should happen
     */
    public function testGetValueWithNonExistentCode(): void
    {
        $actual = 'foo';
        $expected = 'foo is not a code set in settings repository.';

        self::expectExceptionMessage($expected);
        $this->settingsManager->getValue($actual);
    }
}
