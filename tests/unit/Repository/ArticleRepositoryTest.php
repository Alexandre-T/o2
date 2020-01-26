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

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Article repository test.
 *
 * @internal
 * @coversDefaultClass
 */
class ArticleRepositoryTest extends KernelTestCase
{
    /**
     * Article Repository.
     *
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Setup the repository before each test.
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->articleRepository = $this->entityManager->getRepository(Article::class);
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
        $expected = $actual = 'CRED0010';
        $article = $this->articleRepository->findOneByCode($actual);
        self::assertNotNull($article);
        self::assertInstanceOf(Article::class, $article);
        self::assertSame($expected, $article->getCode());

        $actual = 'non-existent';
        $article = $this->articleRepository->findOneByCode($actual);
        self::assertNull($article);
    }
}
