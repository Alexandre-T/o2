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

use App\Entity\StatusOrder;
use App\Repository\StatusOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * StatusOrder repository test.
 *
 * @internal
 * @coversDefaultClass
 */
class StatusOrderRepositoryTest extends KernelTestCase
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * StatusOrder Repository.
     *
     * @var StatusOrderRepository
     */
    private $statusOrderRepository;

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

        $this->statusOrderRepository = $this->entityManager->getRepository(StatusOrder::class);
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
    public function testFindOneCanceled(): void
    {
        $statusOrder = $this->statusOrderRepository->findOneCanceled();
        self::assertNotNull($statusOrder);
        self::assertInstanceOf(StatusOrder::class, $statusOrder);
        self::assertTrue($statusOrder->isCanceled());
        self::assertFalse($statusOrder->isPaid());
        self::assertFalse($statusOrder->isPending());
    }

    /**
     * Test the findOnePaid method.
     */
    public function testFindOnePaid(): void
    {
        $statusOrder = $this->statusOrderRepository->findOnePaid();
        self::assertNotNull($statusOrder);
        self::assertInstanceOf(StatusOrder::class, $statusOrder);
        self::assertFalse($statusOrder->isCanceled());
        self::assertTrue($statusOrder->isPaid());
        self::assertFalse($statusOrder->isPending());
    }

    /**
     * Test the findOnePaid method.
     */
    public function testFindOnePending(): void
    {
        $statusOrder = $this->statusOrderRepository->findOnePending();
        self::assertNotNull($statusOrder);
        self::assertInstanceOf(StatusOrder::class, $statusOrder);
        self::assertFalse($statusOrder->isCanceled());
        self::assertFalse($statusOrder->isPaid());
        self::assertTrue($statusOrder->isPending());
    }
}
