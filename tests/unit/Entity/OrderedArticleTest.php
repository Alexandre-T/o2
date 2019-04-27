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

namespace App\Tests\unit\Entity;

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\OrderedArticle;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Ordered article test.
 *
 * @internal
 * @coversDefaultClass
 */
class OrderedArticleTest extends Unit
{
    /**
     * Order to test.
     *
     * @var OrderedArticle
     */
    protected $orderedArticle;

    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Before each test, order is created.
     */
    protected function setUp(): void
    {
        $this->orderedArticle = new OrderedArticle();
        parent::setUp();
    }

    /**
     * After each test, order is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->orderedArticle = null;
    }

    /**
     * Test the method Article getter and setter.
     */
    public function testArticle(): void
    {
        $expected = $actual = new Article();
        self::assertEquals($this->orderedArticle, $this->orderedArticle->setArticle($actual));
        self::assertEquals($expected, $this->orderedArticle->getArticle());
    }

    /**
     * Test the method Order getter and setter.
     */
    public function testOrder(): void
    {
        $expected = $actual = new Order();
        self::assertEquals($this->orderedArticle, $this->orderedArticle->setOrder($actual));
        self::assertEquals($expected, $this->orderedArticle->getOrder());
    }

    /**
     * Test the quantity getter and setter.
     */
    public function testQuantity(): void
    {
        $expected = $actual = 42;
        self::assertEquals($this->orderedArticle, $this->orderedArticle->setQuantity($actual));
        self::assertEquals($expected, $this->orderedArticle->getQuantity());
    }
}
