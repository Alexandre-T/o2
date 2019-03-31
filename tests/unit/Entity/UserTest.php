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

use App\Entity\User;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * @internal
 * @coversNothing
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
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');
        self::assertNull($this->user->getId());
        self::assertNotNull($this->user->getLabel());
        self::assertEmpty($this->user->getLabel());
        self::assertNull($this->user->getGivenName());
        self::assertNull($this->user->getMail());
        self::assertNull($this->user->getName());
        self::assertNull($this->user->getPassword());
        self::assertNull($this->user->getPlainPassword());
        self::assertNull($this->user->getSalt());
        self::assertNull($this->user->getSociety());
        self::assertEquals(User::PHYSIC, $this->user->getType());
        self::assertIsBool($this->user->getType());
        self::assertNotNull($this->user->getUsername());
        self::assertEmpty($this->user->getUsername());

        $this->tester->wantToTest('roles are well initialized');
        self::assertNotNull($this->user->getRoles());
        self::assertEquals(['ROLE_USER'], $this->user->getRoles());
        self::assertFalse($this->user->isAdmin());
        self::assertTrue($this->user->isClient());
        self::assertFalse($this->user->isComptable());
        self::assertFalse($this->user->isProgrammer());
    }

    /**
     * Tests label.
     */
    public function testLabel(): void
    {
        $this->tester->wantToTest('User label');

        $this->user->setSociety('society');
        $this->user->setType(User::MORAL);
        self::assertEquals('society', $this->user->getLabel());

        $this->user->setGivenName('john');
        self::assertEquals('society', $this->user->getLabel());

        $this->user->setType(User::PHYSIC);
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
        $this->user->setType(User::MORAL);
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
        self::assertNotEquals($this->user->getPassword(), $user->getPassword());
        self::assertNotEquals($this->user->getPlainPassword(), $user->getPlainPassword());

        $this->tester->wantToTest('password are never serialized');
        self::assertNotContains('bar', $serialize);
        self::assertNotContains('foo', $serialize);
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
        self::assertTrue($this->user->isClient());

        //Set ROLE_COMPTABLE and test.
        self::assertEquals($this->user, $this->user->setRoles([User::ROLE_COMPTABLE]));
        self::assertTrue($this->user->isComptable());
        self::assertTrue($this->user->isClient());

        //Add ROLE_PROGRAMMER and test.
        self::assertEquals($this->user, $this->user->addRole(User::ROLE_PROGRAMMER));
        self::assertTrue($this->user->isComptable());
        self::assertTrue($this->user->isProgrammer());
        self::assertTrue($this->user->isClient());

        //Remove ALL roles and test.
        self::assertEquals($this->user, $this->user->setRoles([]));
        self::assertFalse($this->user->isAdmin());
        self::assertTrue($this->user->isClient());
        self::assertFalse($this->user->isComptable());
        self::assertFalse($this->user->isProgrammer());
    }
}
