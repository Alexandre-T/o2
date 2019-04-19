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
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Status order to test.
     *
     * @var Article
     */
    protected $article;

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
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        self::assertNull($this->article->getCode());
        self::assertNull($this->article->getCredit());
        self::assertNull($this->article->getId());
        self::assertNull($this->article->getPrice());
        self::assertNull($this->article->getVat());
    }

    /**
     * Test Code setter and getter.
     */
    public function testCode(): void
    {
        $actual = $expected = 'code';

        self::assertEquals($this->article, $this->article->setCode($actual));
        self::assertEquals($expected, $this->article->getCode());
    }

    /**
     * Test Credit setter and getter.
     */
    public function testCredit(): void
    {
        $expected = $actual = 42;

        self::assertEquals($this->article, $this->article->setCredit($actual));
        self::assertEquals($expected, $this->article->getCredit());
    }
}
