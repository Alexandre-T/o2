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
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Price trait unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class PriceTest extends Unit
{
    /**
     * Order uses trait to test.
     *
     * @var Order
     */
    protected $order;

    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Before each test, order is created.
     */
    protected function setUp(): void
    {
        $this->order = new Order();
        parent::setUp();
    }

    /**
     * After each test, order is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->order = null;
    }

    /**
     * Test amount getter.
     */
    public function testAmount(): void
    {
        $this->order->setPrice(42.84);
        $this->order->setVat(42.84 * 0.2);
        self::assertEquals(42.84, $this->order->getPrice());
        self::assertEquals(8.568, $this->order->getVat());
        self::assertEquals(51.408, $this->order->getAmount());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');
        self::assertNull($this->order->getPrice());
        self::assertNull($this->order->getVat());
        self::assertNotNull($this->order->getAmount());
        self::assertEmpty($this->order->getAmount());
        self::assertEquals(0, $this->order->getAmount());
        self::assertIsFloat($this->order->getAmount());
    }

    /**
     * Test the copy price method.
     */
    public function testCopyPrice(): void
    {
        $bill = new Bill();
        $bill->setPrice(42.42);
        $bill->setVat(12.12);

        self::assertEquals($this->order, $this->order->copyPrice($bill));
        self::assertEquals(42.42, $this->order->getPrice());
        self::assertEquals(12.12, $this->order->getVat());

        $bill = new Bill();
        self::assertEquals($this->order, $this->order->copyPrice($bill));
        self::assertNull($this->order->getPrice());
        self::assertNull($this->order->getVat());
    }

    /**
     * Test Price setter and getter.
     */
    public function testPrice(): void
    {
        $actual = $expected = 42.42;
        self::assertEquals($this->order, $this->order->setPrice($actual));
        self::assertEquals($expected, $this->order->getPrice());
        self::assertEquals($expected, $this->order->getAmount());

        $actual = $expected = '42.24';
        self::assertEquals($this->order, $this->order->setPrice($actual));
        self::assertEquals($expected, $this->order->getPrice());
        self::assertEquals(42.24, $this->order->getAmount());
    }

    /**
     * Test Vat setter and getter.
     */
    public function testVat(): void
    {
        $actual = $expected = 42.42;
        self::assertEquals($this->order, $this->order->setVat($actual));
        self::assertEquals($expected, $this->order->getVat());
        self::assertEquals($expected, $this->order->getAmount());

        $actual = $expected = '42.24';
        self::assertEquals($this->order, $this->order->setVat($actual));
        self::assertEquals($expected, $this->order->getVat());
        self::assertEquals(42.24, $this->order->getAmount());
    }
}
