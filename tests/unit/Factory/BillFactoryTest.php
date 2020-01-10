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

use App\Entity\Order;
use App\Entity\User;
use App\Factory\BillFactory;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

/**
 * Bill factory unit tests.
 *
 * @internal
 * @coversDefaultClass
 */
class BillFactoryTest extends Unit
{
    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Test bill factored.
     */
    public function testSimpleFactory(): void
    {
        $order = new Order();
        $order->setPrice(42.42);
        $customer = new User();
        $customer->setName('foo');
        $customer->setLocality('bar');

        $factoredBill = BillFactory::create($order, $customer);
        self::assertSame($order, $factoredBill->getOrder());
        self::assertSame($customer, $factoredBill->getCustomer());
        self::assertSame('foo', $factoredBill->getName());
        self::assertSame('bar', $factoredBill->getLocality());
        self::assertSame(42.42, $factoredBill->getPrice());

        $order->setCustomer($customer);
        $factoredBill = BillFactory::create($order);
        self::assertSame($order, $factoredBill->getOrder());
        self::assertSame($customer, $factoredBill->getCustomer());
        self::assertSame('foo', $factoredBill->getName());
        self::assertSame('bar', $factoredBill->getLocality());
        self::assertSame(42.42, $factoredBill->getPrice());
    }
}
