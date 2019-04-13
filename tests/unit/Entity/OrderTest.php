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

use App\Entity\Order;
use App\Entity\StatusOrder;
use App\Entity\User;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use DateTimeImmutable;

/**
 * Order entity unit tests.
 */
class OrderTest extends Unit
{
    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Order to test.
     *
     * @var Order
     */
    protected $order;

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
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');
        //Credit
        self::assertNull($this->order->getCredits());
        //Customer
        self::assertNull($this->order->getCustomer());
        //Id
        self::assertNull($this->order->getId());
        //isPaid
        self::assertNull($this->order->isPaid());
        //Label
        self::assertNotNull($this->order->getLabel());
        self::assertEquals('000000', $this->order->getLabel());
        //Number
        self::assertNull($this->order->getNumber());
        //PaymentAt
        self::assertNull($this->order->getPaymentAt());
        //Vat
        self::assertNull($this->order->getVat());
    }

    /**
     * Test Credits setter and getter.
     */
    public function testCredits(): void
    {
        $actual = $expected = 42;

        self::assertEquals($this->order, $this->order->setCredits($actual));
        self::assertEquals($expected, $this->order->getCredits());
    }

    /**
     * Test Customer setter and getter.
     */
    public function testCustomer(): void
    {
        $actual = $expected = new User();

        self::assertEquals($this->order, $this->order->setCustomer($actual));
        self::assertEquals($expected, $this->order->getCustomer());
    }

    /**
     * Tests label.
     */
    public function testLabel(): void
    {
        $this->tester->wantToTest('Order label');

        $actual = $expected = 42;
        self::assertEquals($this->order, $this->order->setNumber($actual));
        self::assertEquals($expected, $this->order->getNumber());

        $expected = '000042';
        self::assertEquals($expected, $this->order->getLabel());
    }

    /**
     * Test PaymentAt setter and getter.
     */
    public function testPaymentAt(): void
    {
        $actual = $expected = new DateTimeImmutable();

        self::assertEquals($this->order, $this->order->setPaymentAt($actual));
        self::assertEquals($expected, $this->order->getPaymentAt());
    }

    /**
     * Test Price setter and getter.
     */
    public function testPrice(): void
    {
        $actual = $expected = 42.42;

        self::assertEquals($this->order, $this->order->setPrice($actual));
        self::assertEquals($expected, $this->order->getPrice());
    }

    /**
     * Test Price setter and getter.
     */
    public function testStatusCredit(): void
    {
        $actual = true;

        self::assertEquals($this->order, $this->order->setStatusCredit($actual));
        self::assertTrue($this->order->getStatusCredit());
        self::assertTrue($this->order->isCredited());
    }

    /**
     * Test StatusOrder setter and getter.
     */
    public function testStatusOrder(): void
    {
        $actual = $expected = new StatusOrder();

        self::assertEquals($this->order, $this->order->setStatusOrder($actual));
        self::assertEquals($expected, $this->order->getStatusOrder());

        $actual->setPaid(true);
        self::assertTrue($this->order->isPaid());

        $actual->setCanceled(true);
        self::assertFalse($this->order->isPaid());
    }

    /**
     * Test Vat setter and getter.
     */
    public function testVat(): void
    {
        $actual = $expected = 42.42;

        self::assertEquals($this->order, $this->order->setVat($actual));
        self::assertEquals($expected, $this->order->getVat());
    }
}
