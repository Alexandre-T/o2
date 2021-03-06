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

use App\Entity\Article;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Status order resource unit test.
 *
 * @internal
 * @coversDefaultClass
 */
class ArticleTest extends Unit
{
    /**
     * Status order to test.
     *
     * @var Article
     */
    protected $article;

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
        $this->article = new Article();
        parent::setUp();
    }

    /**
     * After each test, user is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->article = null;
    }

    /**
     * Test Code setter and getter.
     */
    public function testCode(): void
    {
        $actual = $expected = 'code';

        self::assertSame($this->article, $this->article->setCode($actual));
        self::assertSame($expected, $this->article->getCode());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        self::assertNull($this->article->getCode());
        self::assertNull($this->article->getCredit());
        self::assertNull($this->article->getId());
        self::assertNull($this->article->getPrice());
    }

    /**
     * Test Credit setter and getter.
     */
    public function testCredit(): void
    {
        $expected = $actual = 42;

        self::assertSame($this->article, $this->article->setCredit($actual));
        self::assertSame($expected, $this->article->getCredit());
    }

    /**
     * Test Price setter and getter.
     */
    public function testPrice(): void
    {
        $expected = $actual = 42.42;

        self::assertSame($this->article, $this->article->setPrice($actual));
        self::assertSame($expected, $this->article->getPrice());
    }
}
