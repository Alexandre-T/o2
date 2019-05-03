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

use App\Entity\File;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * File entity unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class FileTest extends Unit
{
    /**
     * File to test.
     *
     * @var File
     */
    protected $file;

    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Before each test, file is created.
     */
    protected function setUp(): void
    {
        $this->file = new File();
        parent::setUp();
    }

    /**
     * After each test, file is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->file = null;
    }

    /**
     * Test constructor.
     */
    public function testConstructor(): void
    {
        self::assertNull($this->file->getId());
        self::assertNull($this->file->getName());
        self::assertNull($this->file->getMimeType());
        self::assertNull($this->file->getPath());
        self::assertNull($this->file->getSize());
    }

    /**
     * Test the method GetMimeType.
     */
    public function testGetMimeType(): void
    {
        $actual = $expected = 'mimeType';
        self::assertEquals($this->file, $this->file->setMimeType($actual));
        self::assertEquals($expected, $this->file->getMimeType());
    }

    /**
     * Test the method GetName.
     */
    public function testGetName(): void
    {
        $actual = $expected = 'name';
        self::assertEquals($this->file, $this->file->setName($actual));
        self::assertEquals($expected, $this->file->getName());
    }

    /**
     * Test the method GetPath.
     */
    public function testGetPath(): void
    {
        $actual = $expected = 'path';
        self::assertEquals($this->file, $this->file->setPath($actual));
        self::assertEquals($expected, $this->file->getPath());
    }

    /**
     * Test the method GetSize.
     */
    public function testGetSize(): void
    {
        $actual = $expected = '42.42';
        self::assertEquals($this->file, $this->file->setSize($actual));
        self::assertEquals($expected, $this->file->getSize());

        $actual = $expected = 42.24;
        self::assertEquals($this->file, $this->file->setSize($actual));
        self::assertEquals($expected, $this->file->getSize());
    }
}