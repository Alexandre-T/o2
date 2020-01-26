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

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * User repository test.
 *
 * @internal
 * @coversDefaultClass
 */
class UserRepositoryTest extends KernelTestCase
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * User Repository.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Close entity manager to avoid memory leaks.
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    /**
     * Test the findByMail method.
     */
    public function testFindByMail(): void
    {
        $user = $this->userRepository->findOneByMail('customer-1@example.org');
        self::assertNotNull($user);
        self::assertInstanceOf(User::class, $user);
    }

    /**
     * Test the support class.
     */
    public function testSupportClass(): void
    {
        self::assertTrue($this->userRepository->supportsClass(User::class));
        self::assertFalse($this->userRepository->supportsClass(UserRepository::class));
    }
}
