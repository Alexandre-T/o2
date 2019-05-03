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
use App\Entity\Programmation;
use App\Entity\User;
use App\Model\ProgrammationInterface;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use DateTimeImmutable;

/**
 * Programmation entity unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class ProgrammationTest extends Unit
{
    /**
     * Programmation to test.
     *
     * @var Programmation
     */
    protected $programmation;

    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Before each test, programmation is created.
     */
    protected function setUp(): void
    {
        $this->programmation = new Programmation();
        parent::setUp();
    }

    /**
     * After each test, programmation is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->programmation = null;
    }

    /**
     * Test constructor.
     */
    public function testConstructor(): void
    {
        self::assertNull($this->programmation->getId());
        self::assertNull($this->programmation->getCredit());
        self::assertNull($this->programmation->getCreatedAt());
        self::assertNull($this->programmation->getCustomer());
        self::assertNull($this->programmation->getComment());
        self::assertNull($this->programmation->getCylinderCapacity());
        self::assertNull($this->programmation->getFinalFile());
        self::assertNull($this->programmation->getMake());
        self::assertNull($this->programmation->getModel());
        self::assertNull($this->programmation->getOdb());
        self::assertNull($this->programmation->getOdometer());
        self::assertNull($this->programmation->getOriginalFile());
        self::assertNull($this->programmation->getPower());
        self::assertNull($this->programmation->getProtocol());
        self::assertNull($this->programmation->getRead());
        self::assertNull($this->programmation->getReaderTool());
        self::assertNull($this->programmation->getSerial());
        self::assertNull($this->programmation->getVersion());
        self::assertNull($this->programmation->getYear());

        self::assertFalse($this->programmation->isEdcOff());
        self::assertFalse($this->programmation->isEgrOff());
        self::assertFalse($this->programmation->isEthanol());
        self::assertFalse($this->programmation->isFapOff());
        self::assertFalse($this->programmation->isGearAutomatic());
        self::assertFalse($this->programmation->isStageOne());

        self::assertFalse($this->programmation->isEdcStopped());
        self::assertFalse($this->programmation->isEgrStopped());
        self::assertFalse($this->programmation->isEthanolDone());
        self::assertFalse($this->programmation->isFapStopped());
        self::assertFalse($this->programmation->isStageOneDone());

        self::assertNotNull($this->programmation->getLabel());
        self::assertEmpty($this->programmation->getLabel());
        self::assertIsString($this->programmation->getLabel());
    }

    /**
     * Test the method GetComment.
     */
    public function testGetComment(): void
    {
        $actual = $expected = 'comment';
        self::assertEquals($this->programmation, $this->programmation->setComment($actual));
        self::assertEquals($expected, $this->programmation->getComment());
    }

    /**
     * Test the method GetCredit.
     */
    public function testGetCredit(): void
    {
        $expected = 5;

        $this->programmation->setEdcOff(true);
        $this->programmation->setEgrOff(false);
        $this->programmation->setFapOff(false);
        $this->programmation->setEthanol(false);
        $this->programmation->setStageOne(false);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $this->programmation->setEdcOff(false);
        $this->programmation->setEgrOff(true);
        $this->programmation->setFapOff(false);
        $this->programmation->setEthanol(false);
        $this->programmation->setStageOne(false);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $this->programmation->setEdcOff(false);
        $this->programmation->setEgrOff(false);
        $this->programmation->setFapOff(true);
        $this->programmation->setEthanol(false);
        $this->programmation->setStageOne(false);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $this->programmation->setEdcOff(false);
        $this->programmation->setEgrOff(true);
        $this->programmation->setFapOff(true);
        $this->programmation->setEthanol(false);
        $this->programmation->setStageOne(false);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $expected = 10;
        $this->programmation->setEdcOff(false);
        $this->programmation->setEgrOff(false);
        $this->programmation->setFapOff(false);
        $this->programmation->setEthanol(true);
        $this->programmation->setStageOne(false);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $this->programmation->setEdcOff(false);
        $this->programmation->setEgrOff(false);
        $this->programmation->setFapOff(false);
        $this->programmation->setEthanol(false);
        $this->programmation->setStageOne(true);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $this->programmation->setEdcOff(false);
        $this->programmation->setEgrOff(false);
        $this->programmation->setFapOff(false);
        $this->programmation->setEthanol(true);
        $this->programmation->setStageOne(true);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $expected = 15;
        $this->programmation->setEdcOff(false);
        $this->programmation->setEgrOff(true);
        $this->programmation->setFapOff(true);
        $this->programmation->setEthanol(true);
        $this->programmation->setStageOne(true);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());

        $expected = 20;
        $this->programmation->setEdcOff(true);
        $this->programmation->setEgrOff(true);
        $this->programmation->setFapOff(true);
        $this->programmation->setEthanol(true);
        $this->programmation->setStageOne(true);
        self::assertEquals($this->programmation, $this->programmation->refreshCost());
        self::assertEquals($expected, $this->programmation->getCredit());
    }

    /**
     * Test the method GetCustomer.
     */
    public function testGetCustomer(): void
    {
        $actual = $expected = new User();
        self::assertEquals($this->programmation, $this->programmation->setCustomer($actual));
        self::assertEquals($expected, $this->programmation->getCustomer());
    }

    /**
     * Test the method GetCylinderCapacity.
     */
    public function testGetCylinderCapacity(): void
    {
        $actual = $expected = '33.33';
        self::assertEquals($this->programmation, $this->programmation->setCylinderCapacity($actual));
        self::assertEquals($expected, $this->programmation->getCylinderCapacity());

        $actual = $expected = 42.42;
        self::assertEquals($this->programmation, $this->programmation->setCylinderCapacity($actual));
        self::assertEquals($expected, $this->programmation->getCylinderCapacity());
    }

    /**
     * Test the method getDelivered.
     */
    public function testGetDelivered(): void
    {
        $actual = $expected = new DateTimeImmutable();
        self::assertEquals($this->programmation, $this->programmation->setDeliveredAt($actual));
        self::assertEquals($expected, $this->programmation->getDeliveredAt());
    }

    /**
     * Test the method GetFinalProgrammation.
     */
    public function testGetFinalProgrammation(): void
    {
        $actual = $expected = new File();
        self::assertEquals($this->programmation, $this->programmation->setFinalFile($actual));
        self::assertEquals($expected, $this->programmation->getFinalFile());
    }

    /**
     * Test the method GetMake.
     */
    public function testGetMake(): void
    {
        $actual = $expected = 'make';
        self::assertEquals($this->programmation, $this->programmation->setMake($actual));
        self::assertEquals($expected, $this->programmation->getMake());
    }

    /**
     * Test the method GetModel.
     */
    public function testGetModel(): void
    {
        $actual = $expected = 'model';
        self::assertEquals($this->programmation, $this->programmation->setModel($actual));
        self::assertEquals($expected, $this->programmation->getModel());
    }

    /**
     * Test the method GetOdb.
     */
    public function testGetOdb(): void
    {
        $actual = $expected = ProgrammationInterface::ODB_BOOT;
        self::assertEquals($this->programmation, $this->programmation->setOdb($actual));
        self::assertEquals($expected, $this->programmation->getOdb());
    }

    /**
     * Test the method GetOdometer.
     */
    public function testGetOdometer(): void
    {
        $actual = $expected = 33000;
        self::assertEquals($this->programmation, $this->programmation->setOdometer($actual));
        self::assertEquals($expected, $this->programmation->getOdometer());
    }

    /**
     * Test the method GetOriginalProgrammation.
     */
    public function testGetOriginalProgrammation(): void
    {
        $actual = $expected = new File();
        self::assertEquals($this->programmation, $this->programmation->setOriginalFile($actual));
        self::assertEquals($expected, $this->programmation->getOriginalFile());
    }

    /**
     * Test the method GetPower.
     */
    public function testGetPower(): void
    {
        $actual = $expected = 800;
        self::assertEquals($this->programmation, $this->programmation->setPower($actual));
        self::assertEquals($expected, $this->programmation->getPower());
    }

    /**
     * Test the method GetProtocol.
     */
    public function testGetProtocol(): void
    {
        $actual = $expected = 'protocol';
        self::assertEquals($this->programmation, $this->programmation->setProtocol($actual));
        self::assertEquals($expected, $this->programmation->getProtocol());
    }

    /**
     * Test the method GetRead.
     */
    public function testGetRead(): void
    {
        $actual = $expected = ProgrammationInterface::READ_VIRTUAL;
        self::assertEquals($this->programmation, $this->programmation->setRead($actual));
        self::assertEquals($expected, $this->programmation->getRead());
    }

    /**
     * Test the method GetReader.
     */
    public function testGetReaderTool(): void
    {
        $actual = $expected = 'reader';
        self::assertEquals($this->programmation, $this->programmation->setReaderTool($actual));
        self::assertEquals($expected, $this->programmation->getReaderTool());
    }

    /**
     * Test the method GetSerial.
     */
    public function testGetSerial(): void
    {
        $actual = $expected = 'serial';
        self::assertEquals($this->programmation, $this->programmation->setSerial($actual));
        self::assertEquals($expected, $this->programmation->getSerial());
    }

    /**
     * Test the method GetVersion.
     */
    public function testGetVersion(): void
    {
        $actual = $expected = 'version';
        self::assertEquals($this->programmation, $this->programmation->setVersion($actual));
        self::assertEquals($expected, $this->programmation->getVersion());
    }

    /**
     * Test the method GetYear.
     */
    public function testGetYear(): void
    {
        $actual = $expected = 2019;
        self::assertEquals($this->programmation, $this->programmation->setYear($actual));
        self::assertEquals($expected, $this->programmation->getYear());
    }

    /**
     * Test the method IsEdcOff.
     */
    public function testIsEdcOff(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setEdcOff(true));
        self::assertTrue($this->programmation->isEdcOff());
    }

    /**
     * Test the method IsEdcStopped.
     */
    public function testIsEdcStopped(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setEdcStopped(true));
        self::assertTrue($this->programmation->isEdcStopped());
    }

    /**
     * Test the method IsEgrOff.
     */
    public function testIsEgrOff(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setEgrOff(true));
        self::assertTrue($this->programmation->isEgrOff());
    }

    /**
     * Test the method IsEgrStopped.
     */
    public function testIsEgrStopped(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setEgrStopped(true));
        self::assertTrue($this->programmation->isEgrStopped());
    }

    /**
     * Test the method IsEthanol.
     */
    public function testIsEthanol(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setEthanol(true));
        self::assertTrue($this->programmation->isEthanol());
    }

    /**
     * Test the method IsEthanolDone.
     */
    public function testIsEthanolDone(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setEthanolDone(true));
        self::assertTrue($this->programmation->isEthanolDone());
    }

    /**
     * Test the method IsFapOff.
     */
    public function testIsFapOff(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setFapOff(true));
        self::assertTrue($this->programmation->isFapOff());
    }

    /**
     * Test the method IsFapStopped.
     */
    public function testIsFapStopped(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setFapStopped(true));
        self::assertTrue($this->programmation->isFapStopped());
    }

    /**
     * Test the method IsGearAutomatic.
     */
    public function testIsGearAutomatic(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setGearAutomatic(true));
        self::assertTrue($this->programmation->isGearAutomatic());
    }

    /**
     * Test the method IsStageOne.
     */
    public function testIsStageOne(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setStageOne(true));
        self::assertTrue($this->programmation->isStageOne());
    }

    /**
     * Test the method IsStageOneDone.
     */
    public function testIsStageOneDone(): void
    {
        self::assertEquals($this->programmation, $this->programmation->setStageOneDone(true));
        self::assertTrue($this->programmation->isStageOneDone());
    }
}
