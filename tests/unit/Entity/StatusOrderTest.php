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

use App\Entity\StatusOrder;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Status order resource unit test.
 *
 * @internal
 * @coversDefaultClass
 */
class StatusOrderTest extends Unit
{
    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Status order to test.
     *
     * @var StatusOrder
     */
    protected $status;

    /**
     * Before each test, user is created.
     */
    protected function setUp(): void
    {
        $this->status = new StatusOrder();
        parent::setUp();
    }

    /**
     * After each test, user is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->status = null;
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        self::assertNull($this->status->getCode());
        self::assertNull($this->status->getId());
        self::assertFalse($this->status->isCanceled());
        self::assertFalse($this->status->isPaid());
    }

    /**
     * Test Code setter and getter.
     */
    public function testCode(): void
    {
        $actual = $expected = 'code';

        self::assertEquals($this->status, $this->status->setCode($actual));
        self::assertEquals($expected, $this->status->getCode());
    }

    /**
     * Test Canceled setter and getter.
     */
    public function testCanceled(): void
    {
        $actual = true;

        self::assertEquals($this->status, $this->status->setCanceled($actual));
        self::assertTrue($this->status->isCanceled());
    }

    /**
     * Test Paid setter and getter.
     */
    public function testPaid(): void
    {
        $actual = true;

        self::assertEquals($this->status, $this->status->setPaid($actual));
        self::assertTrue($this->status->isPaid());
    }

    /**
     * Test Pending setter and getter.
     */
    public function testPending(): void
    {
        $actual = true;

        self::assertEquals($this->status, $this->status->setPending($actual));
        self::assertTrue($this->status->isPending());
    }
}
