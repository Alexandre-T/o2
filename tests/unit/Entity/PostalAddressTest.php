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
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Postal address trait unit tests.
 *
 * @internal
 * @covers
 */
class PostalAddressTest extends Unit
{
    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Order uses trait to test.
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
        //Country
        self::assertNull($this->order->getCountry());
        //Complement
        self::assertNull($this->order->getComplement());
        //Locality
        self::assertNull($this->order->getLocality());
        //Postal code
        self::assertNull($this->order->getPostalCode());
        //Street address
        self::assertNull($this->order->getStreetAddress());
    }

    /**
     * Test Complement setter and getter.
     */
    public function testComplement(): void
    {
        $actual = $expected = 'complement';

        self::assertEquals($this->order, $this->order->setComplement($actual));
        self::assertEquals($expected, $this->order->getComplement());
    }

    /**
     * Test Country setter and getter.
     */
    public function testCountry(): void
    {
        $actual = $expected = 'FR';

        self::assertEquals($this->order, $this->order->setCountry($actual));
        self::assertEquals($expected, $this->order->getCountry());
    }

    /**
     * Test Locality setter and getter.
     */
    public function testLocality(): void
    {
        $actual = $expected = 'locality';

        self::assertEquals($this->order, $this->order->setLocality($actual));
        self::assertEquals($expected, $this->order->getLocality());
    }

    /**
     * Test PostalCode setter and getter.
     */
    public function testPostalCode(): void
    {
        $actual = $expected = '33000';

        self::assertEquals($this->order, $this->order->setPostalCode($actual));
        self::assertEquals($expected, $this->order->getPostalCode());
    }

    /**
     * Test StreetAddress setter and getter.
     */
    public function testStreetAddress(): void
    {
        $actual = $expected = 'address';

        self::assertEquals($this->order, $this->order->setStreetAddress($actual));
        self::assertEquals($expected, $this->order->getStreetAddress());
    }
}
