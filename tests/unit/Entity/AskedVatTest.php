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

namespace App\Tests\Entity;

use App\Entity\AskedVat;
use App\Entity\PersonInterface;
use App\Entity\User;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use DateTimeImmutable;

/**
 * Asked vat unit test.
 *
 * @internal
 * @coversDefaultClass
 */
class AskedVatTest extends Unit
{
    /**
     * AskedVat to test.
     *
     * @var AskedVat
     */
    protected $asked;

    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Before each test, user is created.
     */
    protected function setUp(): void
    {
        $this->asked = new AskedVat();
        parent::setUp();
    }

    /**
     * After each test, user is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->asked = null;
    }

    /**
     * Test Accountant setter and getter.
     */
    public function testAccountant(): void
    {
        $actual = $expected = new User();

        self::assertSame($this->asked, $this->asked->setAccountant($actual));
        self::assertSame($expected, $this->asked->getAccountant());
    }

    /**
     * Test Code setter and getter.
     */
    public function testCode(): void
    {
        $actual = $expected = 'code';

        self::assertSame($this->asked, $this->asked->setCode($actual));
        self::assertSame($expected, $this->asked->getCode());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        self::assertNull($this->asked->getAccountant());
        self::assertNull($this->asked->getCode());
        self::assertInstanceOf(DateTimeImmutable::class, $this->asked->getCreatedAt());
        self::assertNull($this->asked->getCustomer());
        self::assertNull($this->asked->getId());
        self::assertEmpty($this->asked->getLabel());
        self::assertSame(AskedVat::UNDECIDED, $this->asked->getStatus());
    }

    /**
     * Test Customer setter and getter.
     */
    public function testCustomer(): void
    {
        $actual = $expected = new User();
        $actual
            ->setName('Doe')
            ->setGivenName('John')
            ->setType(PersonInterface::PHYSIC);

        self::assertSame($this->asked, $this->asked->setCustomer($actual));
        self::assertSame($expected, $this->asked->getCustomer());
        self::assertSame('John Doe', $this->asked->getLabel());
    }

    /**
     * Test Status setter and getter.
     */
    public function testStatus(): void
    {
        $actual = $expected = AskedVat::REJECTED;

        self::assertSame($this->asked, $this->asked->setStatus($actual));
        self::assertSame($expected, $this->asked->getStatus());
    }
}
