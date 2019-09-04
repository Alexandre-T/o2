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

namespace App\Tests\unit\Utils;

use App\Entity\Programmation;
use App\Utils\CostCalculator;
use Codeception\Test\Unit;

/**
 * @internal
 * @coversNothing
 */
class CostCalculatorTest extends Unit
{
    /**
     * Cost calculator test.
     */
    public function testConstructor(): void
    {
        $programmation = new Programmation();
        $calculator = new CostCalculator($programmation);
        self::assertEquals(0, $calculator->getCost());
    }

    /**
     * Full programmation.
     */
    public function testGetCostWithAllValue(): void
    {
        $programmation = new Programmation();
        $programmation->setEdcOff(true);
        $programmation->setFapOff(true);
        $programmation->setEgrOff(true);
        $programmation->setStageOne(true);
        $programmation->setEthanol(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(25, $calculator->getCost());
    }

    /**
     * Simple value only.
     */
    public function testGetCostWithOneValueOnly(): void
    {
        $programmation = new Programmation();
        $programmation->setEdcOff(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(5, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setEgrOff(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(5, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setEthanol(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(10, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setFapOff(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(5, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setStageOne(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(10, $calculator->getCost());
    }

    /**
     * Two values without reduction.
     */
    public function testGetCostWithTwoValueWithoutReduction(): void
    {
        $programmation = new Programmation();
        $programmation->setEdcOff(true);
        $programmation->setEgrOff(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(10, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setEdcOff(true);
        $programmation->setEthanol(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(15, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setEdcOff(true);
        $programmation->setFapOff(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(10, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setStageOne(true);
        $programmation->setEdcOff(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(15, $calculator->getCost());
    }

    /**
     * Two values with promotion.
     */
    public function testGetCostWithTwoValueWithPromotion(): void
    {
        $programmation = new Programmation();
        $programmation->setFapOff(true);
        $programmation->setEgrOff(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(5, $calculator->getCost());

        $programmation = new Programmation();
        $programmation->setStageOne(true);
        $programmation->setEthanol(true);
        $calculator = new CostCalculator($programmation);
        self::assertEquals(15, $calculator->getCost());
    }
}
