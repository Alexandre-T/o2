<?php
/**
 * This orderedArticle is part of the O2 Application.
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

use App\Entity\OrderedArticle;
use App\Repository\OrderedArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * OrderedArticle repository test.
 *
 * @internal
 * @coversDefaultClass
 */
class OrderedArticleRepositoryTest extends KernelTestCase
{
    /**
     * OrderedArticle Repository.
     *
     * @var OrderedArticleRepository
     */
    private $orderedArticleRepository;

    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

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

        $this->orderedArticleRepository = $this->entityManager->getRepository(OrderedArticle::class);
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
     * Test the findOneCanceled method.
     */
    public function testFindBy(): void
    {
        $expected = $actual = 3;
        $orderedArticles = $this->orderedArticleRepository->findBy([
            'quantity' => $actual
        ]);
        self::assertNotNull($orderedArticles);
        self::assertIsArray($orderedArticles);
        $orderedArticle = $orderedArticles[0];
        self::assertInstanceOf(OrderedArticle::class, $orderedArticle);
        self::assertEquals($expected, $orderedArticle->getQuantity());
    }
}
