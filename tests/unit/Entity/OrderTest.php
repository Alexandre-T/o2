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
use App\Entity\Bill;
use App\Entity\Order;
use App\Entity\OrderedArticle;
use App\Entity\User;
use App\Model\OrderInterface;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

/**
 * Order entity unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class OrderTest extends Unit
{
    /**
     * Order to test.
     *
     * @var Order
     */
    protected $order;

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
        $this->order = new Order();
        parent::setUp();
    }

    /**
     * After each test, order is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->order = null;
    }

    /**
     * Test ordered article.
     */
    public function testBill(): void
    {
        $bill = new Bill();
        self::assertEquals($this->order, $this->order->addBill($bill));
        self::assertNotEmpty($this->order->getBills());
        self::assertContains($bill, $this->order->getBills());

        $anotherBill = new Bill();
        self::assertEquals($this->order, $this->order->addBill($anotherBill));
        self::assertNotEmpty($this->order->getBills());
        self::assertContains($bill, $this->order->getBills());
        self::assertContains($anotherBill, $this->order->getBills());

        self::assertEquals($this->order, $this->order->removeBill($bill));
        self::assertNotContains($bill, $this->order->getBills());
        self::assertContains($anotherBill, $this->order->getBills());
        self::assertEquals($this->order, $this->order->removeBill($anotherBill));
        self::assertEmpty($this->order->getBills());
    }

    /**
     * Test the constructor.
     */
    public function testConstructor(): void
    {
        $this->tester->wantToTest('properties are well initialized');

        self::assertNotNull($this->order->getBills());
        self::assertEmpty($this->order->getBills());
        self::assertNull($this->order->getCredits());
        self::assertNull($this->order->getCustomer());
        self::assertNull($this->order->getId());
        self::assertNotNull($this->order->getLabel());
        self::assertEquals('000000', $this->order->getLabel());
        self::assertEquals(OrderInterface::CARTED, $this->order->getStatusOrder());
        self::assertNull($this->order->getPayerId());
        self::assertNull($this->order->getPaymentInstruction());
        self::assertNull($this->order->getToken());
        self::assertNull($this->order->getVat());
        self::assertNotEmpty($this->order->getUuid());
        self::assertFalse($this->order->isCanceled());
        self::assertTrue($this->order->isCarted());
        self::assertFalse($this->order->isPaid());
        self::assertFalse($this->order->isPending());
    }

    /**
     * Test Credits setter and getter.
     */
    public function testCredits(): void
    {
        $actual = $expected = 42;

        self::assertEquals($this->order, $this->order->setCredits($actual));
        self::assertEquals($expected, $this->order->getCredits());
    }

    /**
     * Test Customer setter and getter.
     */
    public function testCustomer(): void
    {
        $actual = $expected = new User();

        self::assertEquals($this->order, $this->order->setCustomer($actual));
        self::assertEquals($expected, $this->order->getCustomer());
    }

    /**
     * Test ordered article.
     */
    public function testOrderedArticle(): void
    {
        $actualPrice = 42.0;
        $expectedPrice = 84.0;
        $actualVat = $expectedVat = 8.4;
        $expectedVat = $expectedVat = 16.8;

        $article = new Article();
        $article->setPrice($actualPrice);
        self::assertNull($this->order->getOrderedByArticle($article));
        self::assertNull($this->order->getPrice());

        $orderedArticle = new OrderedArticle();
        $this->order->addOrderedArticle($orderedArticle);
        self::assertNull($this->order->getOrderedByArticle($article));
        self::assertNotNull($this->order->getPrice());
        self::assertEmpty($this->order->getPrice());

        $orderedArticle->setArticle($article);
        self::assertEquals($orderedArticle, $this->order->getOrderedByArticle($article));

        $anotherArticle = new Article();
        $anotherArticle->setPrice($actualPrice);
        $anotherArticle->setVat($actualVat);
        $anotherOrdered = new OrderedArticle();
        $anotherOrdered->setArticle($anotherArticle);
        $anotherOrdered->setQuantity(2);
        self::assertEquals($orderedArticle, $this->order->getOrderedByArticle($article));
        self::assertNull($this->order->getOrderedByArticle($anotherArticle));

        $anotherOrdered->setOrder($this->order);
        self::assertEquals($orderedArticle, $this->order->getOrderedByArticle($article));
        self::assertEquals($anotherOrdered, $this->order->getOrderedByArticle($anotherArticle));
        self::assertEquals($expectedPrice, $this->order->getPrice());
        self::assertEquals($expectedVat, $this->order->getVat());

        self::assertEquals($this->order, $this->order->removeOrderedArticle($orderedArticle));
        self::assertNull($this->order->getOrderedByArticle($article));
        self::assertEquals($expectedPrice, $this->order->getPrice());
        self::assertEquals($expectedVat, $this->order->getVat());
        self::assertEquals($anotherOrdered, $this->order->getOrderedByArticle($anotherArticle));
        self::assertEquals($this->order, $this->order->removeOrderedArticle($anotherOrdered));
        self::assertEquals(0.0, $this->order->getPrice());
        self::assertEquals(0.0, $this->order->getVat());
    }

    /**
     * Test PayerId setter and getter.
     */
    public function testPayerId(): void
    {
        $actual = $expected = 'payer-id';

        self::assertEquals($this->order, $this->order->setPayerId($actual));
        self::assertEquals($expected, $this->order->getPayerId());
    }

    /**
     * Test PaymentInstruction setter and getter.
     */
    public function testPaymentInstruction(): void
    {
        $actual = $expected = new PaymentInstruction(0, '€', 'toto');

        self::assertEquals($this->order, $this->order->setPaymentInstruction($actual));
        self::assertEquals($expected, $this->order->getPaymentInstruction());
    }

    /**
     * Test Price setter and getter.
     */
    public function testPrice(): void
    {
        $actual = $expected = 42.42;

        self::assertEquals($this->order, $this->order->setPrice($actual));
        self::assertEquals($expected, $this->order->getPrice());
        self::assertEquals($this->order, $this->order->refreshPrice());
        self::assertEmpty($this->order->getPrice());
    }

    /**
     * Test Price setter and getter.
     */
    public function testStatusCredit(): void
    {
        $actual = true;

        self::assertEquals($this->order, $this->order->setStatusCredit($actual));
        self::assertTrue($this->order->isCredited());
    }

    /**
     * Test StatusOrder setter and getter.
     */
    public function testStatusOrder(): void
    {
        self::assertEquals($this->order, $this->order->setStatusOrder(OrderInterface::CANCELED));
        self::assertEquals(OrderInterface::CANCELED, $this->order->getStatusOrder());
        self::assertTrue($this->order->isCanceled());
        self::assertFalse($this->order->isCarted());
        self::assertFalse($this->order->isPaid());
        self::assertFalse($this->order->isPending());

        self::assertEquals($this->order, $this->order->setStatusOrder(OrderInterface::CARTED));
        self::assertEquals(OrderInterface::CARTED, $this->order->getStatusOrder());
        self::assertFalse($this->order->isCanceled());
        self::assertTrue($this->order->isCarted());
        self::assertFalse($this->order->isPaid());
        self::assertFalse($this->order->isPending());

        self::assertEquals($this->order, $this->order->setStatusOrder(OrderInterface::PENDING));
        self::assertEquals(OrderInterface::PENDING, $this->order->getStatusOrder());
        self::assertFalse($this->order->isCanceled());
        self::assertFalse($this->order->isCarted());
        self::assertFalse($this->order->isPaid());
        self::assertTrue($this->order->isPending());

        self::assertEquals($this->order, $this->order->setStatusOrder(OrderInterface::PAID));
        self::assertEquals(OrderInterface::PAID, $this->order->getStatusOrder());
        self::assertFalse($this->order->isCanceled());
        self::assertFalse($this->order->isCarted());
        self::assertTrue($this->order->isPaid());
        self::assertFalse($this->order->isPending());
    }

    /**
     * Test Token setter and getter.
     */
    public function testToken(): void
    {
        $actual = $expected = 'token';

        self::assertEquals($this->order, $this->order->setToken($actual));
        self::assertEquals($expected, $this->order->getToken());
    }

    /**
     * Test Uuid refresh and getter.
     */
    public function testUuid(): void
    {
        $actual = $this->order->getUuid();

        self::assertEquals($this->order, $this->order->refreshUuid());
        self::assertNotEquals($actual, $this->order->getUuid());
    }

    /**
     * Test Vat setter and getter.
     */
    public function testVat(): void
    {
        $actual = $expected = 42.42;

        self::assertEquals($this->order, $this->order->setVat($actual));
        self::assertEquals($expected, $this->order->getVat());
        self::assertEquals($this->order, $this->order->refreshVat());
        self::assertEmpty($this->order->getVat());
    }
}
