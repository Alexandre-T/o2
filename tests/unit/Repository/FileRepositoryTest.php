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

use App\Entity\File;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * File repository test.
 *
 * @internal
 * @coversDefaultClass
 */
class FileRepositoryTest extends KernelTestCase
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * File Repository.
     *
     * @var FileRepository
     */
    private $fileRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->fileRepository = $this->entityManager->getRepository(File::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    /**
     * Test the findOneCanceled method.
     */
    public function testFindBy(): void
    {
        $expected = $actual = 1;
        $file = $this->fileRepository->findOneBy([
            'identifier' => $actual,
        ]);
        self::assertNotNull($file);
        self::assertInstanceOf(File::class, $file);
        self::assertSame($expected, $file->getId());

        $actual = 9999;
        $file = $this->fileRepository->findOneBy([
            'identifier' => $actual,
        ]);
        self::assertNull($file);
    }
}
