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
use App\Entity\PersonInterface;
use App\Entity\User;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Postal address trait unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class PersonTest extends Unit
{
    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Bill uses trait to test.
     *
     * @var Bill
     */
    protected $bill;

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
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');

        self::assertNull($this->bill->getGivenName());
        self::assertNull($this->bill->getName());
        self::assertNull($this->bill->getSociety());
        self::assertTrue($this->bill->isPhysic());
        self::assertNull($this->bill->getTelephone());
    }

    /**
     * Test GivenName setter and getter.
     */
    public function testGivenName(): void
    {
        $actual = $expected = 'givenName';

        self::assertEquals($this->bill, $this->bill->setGivenName($actual));
        self::assertEquals($expected, $this->bill->getGivenName());
    }

    /**
     * Test Name setter and getter.
     */
    public function testName(): void
    {
        $actual = $expected = 'name';

        self::assertEquals($this->bill, $this->bill->setName($actual));
        self::assertEquals($expected, $this->bill->getName());
    }

    /**
     * Test Society setter and getter.
     */
    public function testSociety(): void
    {
        $actual = $expected = 'society';

        self::assertEquals($this->bill, $this->bill->setSociety($actual));
        self::assertEquals($expected, $this->bill->getSociety());
    }

    /**
     * Test Telephone setter and getter.
     */
    public function testTelephone(): void
    {
        $actual = $expected = '33000';

        self::assertEquals($this->bill, $this->bill->setTelephone($actual));
        self::assertEquals($expected, $this->bill->getTelephone());
    }

    /**
     * Test Type setter and getter.
     */
    public function testType(): void
    {
        self::assertEquals($this->bill, $this->bill->setType(PersonInterface::MORAL));
        self::assertFalse($this->bill->getType());
        self::assertTrue($this->bill->isMoral());
        self::assertFalse($this->bill->isPhysic());

        self::assertEquals($this->bill, $this->bill->setType(PersonInterface::PHYSIC));
        self::assertTrue($this->bill->getType());
        self::assertFalse($this->bill->isMoral());
        self::asserttrue($this->bill->isPhysic());
    }

    /**
     * test copy identity function.
     */
    public function testCopyIdentity(): void
    {
        $actual = new User();

        //null bill copy null user.
        self::assertEquals($this->bill, $this->bill->copyIdentity($actual));
        self::assertNull($this->bill->getGivenName());
        self::assertNull($this->bill->getName());
        self::assertNull($this->bill->getSociety());
        self::assertNull($this->bill->getTelephone());
        self::assertTrue($this->bill->getType());
        self::assertNull($this->bill->getVatNumber());

        //not null copy null
        $this->bill->setGivenName('givenName');
        $this->bill->setName('name');
        $this->bill->setSociety('society');
        $this->bill->setTelephone('telephone');
        $this->bill->setType(true);
        $this->bill->setVatNumber('vatNumber');
        self::assertEquals($this->bill, $this->bill->copyIdentity($actual));
        self::assertNull($this->bill->getGivenName());
        self::assertNull($this->bill->getName());
        self::assertNull($this->bill->getSociety());
        self::assertNull($this->bill->getTelephone());
        self::assertTrue($this->bill->getType());
        self::assertNull($this->bill->getVatNumber());

        //not null copy not null
        $actual->setGivenName('userGN');
        $actual->setName('userN');
        $actual->setSociety('userS');
        $actual->setTelephone('userT');
        $actual->setType(false);
        $actual->setVatNumber('userV');
        $this->bill->setGivenName('givenName');
        $this->bill->setName('name');
        $this->bill->setSociety('society');
        $this->bill->setTelephone('telephone');
        $this->bill->setType(true);
        $this->bill->setVatNumber('vatNumber');
        self::assertEquals($this->bill, $this->bill->copyIdentity($actual));
        self::assertEquals('userGN', $this->bill->getGivenName());
        self::assertEquals('userN', $this->bill->getName());
        self::assertEquals('userS', $this->bill->getSociety());
        self::assertEquals('userT', $this->bill->getTelephone());
        self::assertFalse($this->bill->getType());
        self::assertEquals('userV', $this->bill->getVatNumber());
    }
}
