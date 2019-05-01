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
use App\Entity\PersonInterface;
use App\Entity\Programmation;
use App\Entity\User;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * User entity unit test.
 *
 * @internal
 * @coversDefaultClass
 */
class UserTest extends Unit
{
    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * User to test.
     *
     * @var User
     */
    protected $user;

    /**
     * Before each test, user is created.
     */
    protected function setUp(): void
    {
        $this->user = new User();
        parent::setUp();
    }

    /**
     * After each test, user is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->user = null;
    }

    /**
     * Test user bills.
     */
    public function testBill(): void
    {
        $bill = new Bill();
        self::assertEquals($this->user, $this->user->addBill($bill));
        self::assertNotEmpty($this->user->getBills());
        self::assertContains($bill, $this->user->getBills());

        $anotherBill = new Bill();
        self::assertEquals($this->user, $this->user->addBill($anotherBill));
        self::assertNotEmpty($this->user->getBills());
        self::assertContains($bill, $this->user->getBills());
        self::assertContains($anotherBill, $this->user->getBills());

        self::assertEquals($this->user, $this->user->removeBill($bill));
        self::assertNotContains($bill, $this->user->getBills());
        self::assertContains($anotherBill, $this->user->getBills());
        self::assertEquals($this->user, $this->user->removeBill($anotherBill));
        self::assertEmpty($this->user->getBills());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');
        self::assertNull($this->user->getId());
        self::assertNotNull($this->user->getCredit());
        self::assertEmpty($this->user->getCredit());
        self::assertNotNull($this->user->getLabel());
        self::assertEmpty($this->user->getLabel());
        self::assertNull($this->user->getGivenName());
        self::assertNull($this->user->getMail());
        self::assertNull($this->user->getName());
        self::assertNull($this->user->getPassword());
        self::assertNull($this->user->getPlainPassword());
        self::assertNull($this->user->getResettingAt());
        self::assertNull($this->user->getResettingToken());
        self::assertNull($this->user->getSalt());
        self::assertNull($this->user->getSociety());
        self::assertNull($this->user->getTelephone());
        self::assertEquals(PersonInterface::PHYSIC, $this->user->getType());
        self::assertIsBool($this->user->getType());
        self::assertIsBool($this->user->isTos());
        self::assertFalse($this->user->isTos());
        self::assertNotNull($this->user->getUsername());
        self::assertEmpty($this->user->getUsername());

        $this->tester->wantToTest('roles are well initialized');
        self::assertNotNull($this->user->getRoles());
        self::assertEquals(['ROLE_USER'], $this->user->getRoles());
        self::assertFalse($this->user->isAdmin());
        self::assertTrue($this->user->isCustomer());
        self::assertFalse($this->user->isAccountant());
        self::assertFalse($this->user->isProgrammer());
    }

    /**
     * Test the hasRole function.
     */
    public function testHasRole(): void
    {
        $this->tester->wantToTest('The roles methods');
        self::assertFalse($this->user->hasRole('foo'));

        //Set ROLE_ADMIN and test.
        self::assertEquals($this->user, $this->user->setRoles([User::ROLE_ADMIN]));
        self::assertTrue($this->user->isAdmin());
        self::assertTrue($this->user->isCustomer());

        //Set ROLE_ACCOUNTANT and test.
        self::assertEquals($this->user, $this->user->setRoles([User::ROLE_ACCOUNTANT]));
        self::assertTrue($this->user->isAccountant());
        self::assertTrue($this->user->isCustomer());

        //Add ROLE_PROGRAMMER and test.
        self::assertEquals($this->user, $this->user->addRole(User::ROLE_PROGRAMMER));
        self::assertTrue($this->user->isAccountant());
        self::assertTrue($this->user->isProgrammer());
        self::assertTrue($this->user->isCustomer());

        //Remove ALL roles and test.
        self::assertEquals($this->user, $this->user->setRoles([]));
        self::assertFalse($this->user->isAdmin());
        self::assertTrue($this->user->isCustomer());
        self::assertFalse($this->user->isAccountant());
        self::assertFalse($this->user->isProgrammer());
    }

    /**
     * Tests label.
     */
    public function testLabel(): void
    {
        $this->tester->wantToTest('User label');

        $this->user->setSociety('society');
        $this->user->setType(PersonInterface::MORAL);
        self::assertEquals('society', $this->user->getLabel());

        $this->user->setGivenName('john');
        self::assertEquals('society', $this->user->getLabel());

        $this->user->setType(PersonInterface::PHYSIC);
        self::assertEquals('john', $this->user->getLabel());

        $this->user->setName('doe');
        self::assertEquals('john doe', $this->user->getLabel());

        $this->user->setGivenName(null);
        self::assertEquals('doe', $this->user->getLabel());

        $this->user->setName(null);
        self::assertEquals('', $this->user->getLabel());
    }

    /**
     * Tests mail getter, setter and aliases.
     */
    public function testMail(): void
    {
        self::assertEquals($this->user, $this->user->setMail('mail'));
        self::assertEquals('mail', $this->user->getMail());
        self::assertEquals('mail', $this->user->getUsername());
        self::assertEquals($this->user, $this->user->setUsername('label2'));
        self::assertEquals('label2', $this->user->getUsername());
        self::assertEquals('label2', $this->user->getMail());
    }

    /**
     * Test Orders setters and getter.
     */
    public function testOrders(): void
    {
        $expected = $actual = new Order();

        self::assertEquals($this->user, $this->user->addOrder($actual));
        self::assertTrue($this->user->getOrders()->contains($expected));
        self::assertEquals($this->user, $this->user->removeOrder($actual));
        self::assertFalse($this->user->getOrders()->contains($expected));
    }

    /**
     * Tests password setter and erasing.
     */
    public function testPassword(): void
    {
        $expected = $actual = 'toto';
        self::assertEquals($this->user, $this->user->setPassword($actual));
        self::assertEquals($expected, $this->user->getPassword());
        self::assertEquals($this->user, $this->user->eraseCredentials());
        self::assertNull($this->user->getPlainPassword());
    }

    /**
     * Tests plain password setter and erasing.
     */
    public function testPlainPassword(): void
    {
        //I have to initialize password with a foo value
        $this->user->setPassword('foo');
        //I test the setter
        $expected = $actual = 'bar';
        self::assertEquals($this->user, $this->user->setPlainPassword($actual));
        self::assertEquals($expected, $this->user->getPlainPassword());
        //When setter of plain password was called, password must have been reinitialized.
        self::assertNull($this->user->getPassword());
    }
    
    /**
     * Test user programmations.
     */
    public function testProgrammation(): void
    {
        $programmation = new Programmation();
        self::assertEquals($this->user, $this->user->addProgrammation($programmation));
        self::assertNotEmpty($this->user->getProgrammations());
        self::assertContains($programmation, $this->user->getProgrammations());

        $anotherProgrammation = new Programmation();
        self::assertEquals($this->user, $this->user->addProgrammation($anotherProgrammation));
        self::assertNotEmpty($this->user->getProgrammations());
        self::assertContains($programmation, $this->user->getProgrammations());
        self::assertContains($anotherProgrammation, $this->user->getProgrammations());

        self::assertEquals($this->user, $this->user->removeProgrammation($programmation));
        self::assertNotContains($programmation, $this->user->getProgrammations());
        self::assertContains($anotherProgrammation, $this->user->getProgrammations());
        self::assertEquals($this->user, $this->user->removeProgrammation($anotherProgrammation));
        self::assertEmpty($this->user->getProgrammations());
    }

    /**
     * Test ResettingAt setter and getter.
     */
    public function testResettingAt(): void
    {
        $actual = $expected = new DateTimeImmutable();

        self::assertEquals($this->user, $this->user->setResettingAt($actual));
        self::assertEquals($expected, $this->user->getResettingAt());
    }

    /**
     * Test ResettingToken setter and getter.
     */
    public function testResettingToken(): void
    {
        $actual = $expected = 'resettingToken';

        self::assertEquals($this->user, $this->user->setResettingToken($actual));
        self::assertEquals($expected, $this->user->getResettingToken());
    }

    /**
     * Test serialization.
     */
    public function testSerialization(): void
    {
        $this->user->setCredit(42);
        $this->user->setGivenName('given');
        $this->user->setName('name');
        $this->user->setSociety('society');
        $this->user->setMail('mail@example.org');
        $this->user->setRoles([User::ROLE_ADMIN]);
        $this->user->setType(PersonInterface::MORAL);
        $this->tester->wantToTest('serialization');
        $serialize = $this->user->serialize();
        $user = new User();
        $user->unserialize($serialize);
        self::assertEquals($this->user, $user);

        $this->tester->wantToTest('serialization with password');
        $this->user->setPlainPassword('bar');
        $this->user->setPassword('foo');
        $serialize = $this->user->serialize();
        $user = new User();
        $user->unserialize($serialize);
        self::assertNotEquals($this->user, $user);
        self::assertNotEquals($this->user->getPlainPassword(), $user->getPlainPassword());

        $this->tester->wantToTest('plain-password are never serialized');
        self::assertNotContains('bar', $serialize);
    }

    /**
     * Test Telephone setter and getter.
     */
    public function testTelephone(): void
    {
        $actual = $expected = 'telephone';

        self::assertEquals($this->user, $this->user->setTelephone($actual));
        self::assertEquals($expected, $this->user->getTelephone());
    }

    /**
     * Test TOS setter and getter.
     */
    public function testTos(): void
    {
        self::assertEquals($this->user, $this->user->setTos(true));
        self::assertTrue($this->user->isTos());
    }

    /**
     * Test validate.
     */
    public function testValidateWithNonValidSociety(): void
    {
        /** @var ExecutionContextInterface|MockObject $builder */
        $builder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMockForAbstractClass();
        /** @var ExecutionContextInterface|MockObject $context */
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMockForAbstractClass();

        $builder
            ->expects(self::once())
            ->method('atPath')
            ->with('society')
            ->willReturn($builder)
        ;
        $builder
            ->expects(self::once())
            ->method('addViolation')
        ;

        $context
            ->expects(self::once())
            ->method('buildViolation')
            ->with('error.society.blank', [])
            ->willReturn($builder)
        ;

        $this->user->setType(PersonInterface::MORAL);
        self::assertTrue($this->user->IsSociety());
        self::assertFalse($this->user->IsPhysic());

        $this->user->validate($context);
    }

    /**
     * Test validate.
     */
    public function testValidateWithNull(): void
    {
        /** @var ExecutionContextInterface|MockObject $builder */
        $builder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMockForAbstractClass();
        /** @var ExecutionContextInterface|MockObject $context */
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMockForAbstractClass();

        $builder
            ->expects(self::once())
            ->method('atPath')
            ->with('name')
            ->willReturn($builder)
        ;
        $builder
            ->expects(self::once())
            ->method('addViolation')
        ;

        $context
            ->expects(self::once())
            ->method('buildViolation')
            ->with('error.name.blank', [])
            ->willReturn($builder)
        ;

        $this->user->validate($context);
    }

    /**
     * Test validate.
     */
    public function testValidateWithValidData(): void
    {
        /** @var ExecutionContextInterface|MockObject $context */
        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMockForAbstractClass();

        $context
            ->expects(self::never())
            ->method('buildViolation')
        ;

        //With a valid family name
        $this->user->setName('foo');
        self::assertFalse($this->user->IsSociety());
        self::assertTrue($this->user->IsPhysic());
        $this->user->validate($context);

        //With a valid society
        $this->user->setName(null);
        $this->user->setSociety('bar');
        $this->user->setType(PersonInterface::MORAL);
        $this->user->validate($context);
    }

    /**
     * Test VatNumber setter and getter.
     */
    public function testVatNumber(): void
    {
        $actual = $expected = 'vatNumber';

        self::assertEquals($this->user, $this->user->setVatNumber($actual));
        self::assertEquals($expected, $this->user->getVatNumber());
    }
}
