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
use App\Entity\User;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Postal address trait unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class PostalAddressTest extends Unit
{
    /**
     * Bill uses trait to test.
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
     * Test Complement setter and getter.
     */
    public function testComplement(): void
    {
        $actual = $expected = 'complement';

        self::assertSame($this->bill, $this->bill->setComplement($actual));
        self::assertSame($expected, $this->bill->getComplement());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');
        //Country
        self::assertNull($this->bill->getCountry());
        //Complement
        self::assertNull($this->bill->getComplement());
        //Locality
        self::assertNull($this->bill->getLocality());
        //Postal code
        self::assertNull($this->bill->getPostalCode());
        //Street address
        self::assertNull($this->bill->getStreetAddress());
    }

    /**
     * test copy address function.
     */
    public function testCopyAdress(): void
    {
        $actual = new User();

        //null bill copy null user.
        self::assertSame($this->bill, $this->bill->copyAddress($actual));
        self::assertNull($this->bill->getComplement());
        self::assertNull($this->bill->getCountry());
        self::assertNull($this->bill->getLocality());
        self::assertNull($this->bill->getPostalCode());
        self::assertNull($this->bill->getStreetAddress());

        //not null copy null
        $this->bill->setComplement('complement');
        $this->bill->setCountry('country');
        $this->bill->setLocality('locality');
        $this->bill->setPostalCode('postal code');
        $this->bill->setStreetAddress('street address');
        self::assertSame($this->bill, $this->bill->copyAddress($actual));
        self::assertNull($this->bill->getComplement());
        self::assertNull($this->bill->getCountry());
        self::assertNull($this->bill->getLocality());
        self::assertNull($this->bill->getPostalCode());
        self::assertNull($this->bill->getStreetAddress());

        //not null copy not null
        $actual->setComplement('userC');
        $actual->setCountry('userO');
        $actual->setLocality('userL');
        $actual->setPostalCode('userP');
        $actual->setStreetAddress('userS');
        $this->bill->setComplement('complement');
        $this->bill->setCountry('country');
        $this->bill->setLocality('locality');
        $this->bill->setPostalCode('postal code');
        $this->bill->setStreetAddress('street address');
        self::assertSame($this->bill, $this->bill->copyAddress($actual));
        self::assertSame('userC', $this->bill->getComplement());
        self::assertSame('userO', $this->bill->getCountry());
        self::assertSame('userL', $this->bill->getLocality());
        self::assertSame('userP', $this->bill->getPostalCode());
        self::assertSame('userS', $this->bill->getStreetAddress());
    }

    /**
     * Test Country setter and getter.
     */
    public function testCountry(): void
    {
        $actual = $expected = 'FR';

        self::assertSame($this->bill, $this->bill->setCountry($actual));
        self::assertSame($expected, $this->bill->getCountry());
    }

    /**
     * Test Locality setter and getter.
     */
    public function testLocality(): void
    {
        $actual = $expected = 'locality';

        self::assertSame($this->bill, $this->bill->setLocality($actual));
        self::assertSame($expected, $this->bill->getLocality());
    }

    /**
     * Test PostalCode setter and getter.
     */
    public function testPostalCode(): void
    {
        $actual = $expected = '33000';

        self::assertSame($this->bill, $this->bill->setPostalCode($actual));
        self::assertSame($expected, $this->bill->getPostalCode());
    }

    /**
     * Test StreetAddress setter and getter.
     */
    public function testStreetAddress(): void
    {
        $actual = $expected = 'address';

        self::assertSame($this->bill, $this->bill->setStreetAddress($actual));
        self::assertSame($expected, $this->bill->getStreetAddress());
    }
}
