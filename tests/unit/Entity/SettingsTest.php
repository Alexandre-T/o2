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

use App\Entity\Settings;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use DateTimeImmutable;

/**
 * Status order resource unit test.
 *
 * @internal
 * @coversDefaultClass
 */
class SettingsTest extends Unit
{
    /**
     * Status order to test.
     *
     * @var Settings
     */
    protected $settings;

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
        $this->settings = new Settings();
        parent::setUp();
    }

    /**
     * After each test, user is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->settings = null;
    }

    /**
     * Test Code setter and getter.
     */
    public function testCode(): void
    {
        $actual = $expected = 'code';

        self::assertEquals($this->settings, $this->settings->setCode($actual));
        self::assertEquals($expected, $this->settings->getCode());
        self::assertEquals('settings.code', $this->settings->getLabel());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        self::assertNull($this->settings->getCode());
        self::assertNull($this->settings->getId());
        self::assertNull($this->settings->getValue());
        self::assertNotNull($this->settings->getLabel());
        self::assertEmpty($this->settings->getLabel());
    }

    /**
     * Test Value setter and getter.
     */
    public function testValue(): void
    {
        //Test with a date
        $expected = $actual = new DateTimeImmutable();
        self::assertEquals($this->settings, $this->settings->setValue($actual));
        self::assertEquals($expected, $this->settings->getValue());
        self::assertInstanceOf(DateTimeImmutable::class, $this->settings->getValue());

        //Test with an integer
        $expected = $actual = 42;
        self::assertEquals($this->settings, $this->settings->setValue($actual));
        self::assertEquals($expected, $this->settings->getValue());
        self::assertIsInt($this->settings->getValue());

        //Test with with a string
        $expected = $actual = '42';
        self::assertEquals($this->settings, $this->settings->setValue($actual));
        self::assertEquals($expected, $this->settings->getValue());
        self::assertIsString($this->settings->getValue());

        //Test with with an array
        $expected = $actual = ['foo' => 'bar', 0 => 42];
        self::assertEquals($this->settings, $this->settings->setValue($actual));
        self::assertEquals($expected, $this->settings->getValue());
        self::assertIsArray($this->settings->getValue());

        //Test with with an object
        $expected = $actual = new Settings();
        self::assertEquals($this->settings, $this->settings->setValue($actual));
        self::assertEquals($expected, $this->settings->getValue());
        self::assertIsObject($this->settings->getValue());
        self::assertInstanceOf(Settings::class, $this->settings->getValue());
    }
}
