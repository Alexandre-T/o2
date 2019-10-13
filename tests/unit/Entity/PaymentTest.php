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
use App\Entity\Payment;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Payment entity unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class PaymentTest extends Unit
{
    /**
     * Payment to test.
     *
     * @var Payment
     */
    protected $payment;

    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Before each test, payment is created.
     */
    protected function setUp(): void
    {
        $this->payment = new Payment();
        parent::setUp();
    }

    /**
     * After each test, payment is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->payment = null;
    }

    /**
     * Test order getter and setter.
     */
    public function testOrder(): void
    {
        $order = new Order();
        self::assertEquals($this->payment, $this->payment->setOrder($order));
        self::assertEquals($order, $this->payment->getOrder());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');

        self::assertNull($this->payment->getOrder());
        self::assertNull($this->payment->getId());
    }
}
