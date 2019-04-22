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

use App\Entity\Bill;
use App\Entity\Order;
use App\Entity\User;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use DateTimeImmutable;
use ReflectionClass;
use ReflectionException;

/**
 * Bill entity unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class BillTest extends Unit
{
    /**
     * Bill to test.
     *
     * @var Bill
     */
    protected $bill;

    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Before each test, bill is created.
     */
    protected function setUp(): void
    {
        $this->bill = new Bill();
        parent::setUp();
    }

    /**
     * After each test, bill is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->bill = null;
    }

    /**
     * Test CanceledAt setter and getter.
     */
    public function testCanceledAt(): void
    {
        $actual = $expected = new DateTimeImmutable();

        self::assertEquals($this->bill, $this->bill->setCanceledAt($actual));
        self::assertEquals($expected, $this->bill->getCanceledAt());
        self::assertTrue($this->bill->isCanceled());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');

        self::assertNull($this->bill->getCustomer());
        self::assertNull($this->bill->getId());
        self::assertNull($this->bill->getNumber());
        self::assertNull($this->bill->getOrder());
        self::assertNull($this->bill->getCanceledAt());
        self::assertNull($this->bill->getPaidAt());
        self::assertFalse($this->bill->isCanceled());
        self::assertFalse($this->bill->isPaid());
    }

    /**
     * Test Customer setter and getter.
     */
    public function testCustomer(): void
    {
        $actual = $expected = new User();

        self::assertEquals($this->bill, $this->bill->setCustomer($actual));
        self::assertEquals($expected, $this->bill->getCustomer());
    }

    /**
     * Test Number setter and getter.
     *
     * @throws ReflectionException on reflection error
     */
    public function testNumberAt(): void
    {
        $actual = $expected = 33;

        $reflector = new ReflectionClass(Bill::class);
        $property = $reflector->getProperty('number');
        $property->setAccessible(true);
        $property->setValue($this->bill, $actual);

        self::assertEquals($expected, $this->bill->getNumber());
        self::assertEquals('000033', $this->bill->getLabel());
    }

    /**
     * Test Order setter and getter.
     */
    public function testOrder(): void
    {
        $actual = $expected = new Order();

        self::assertEquals($this->bill, $this->bill->setOrder($actual));
        self::assertEquals($expected, $this->bill->getOrder());
    }

    /**
     * Test PaidAt setter and getter.
     */
    public function testPaidAt(): void
    {
        $actual = $expected = new DateTimeImmutable();

        self::assertEquals($this->bill, $this->bill->setPaidAt($actual));
        self::assertEquals($expected, $this->bill->getPaidAt());
        self::assertTrue($this->bill->isPaid());
    }
}
