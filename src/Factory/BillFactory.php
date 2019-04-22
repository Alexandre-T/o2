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

namespace App\Factory;

use App\Entity\Bill;
use App\Entity\Order;
use App\Entity\User;

class BillFactory
{
    /**
     * Create a new bill.
     *
     * Bill has number only after record on database.
     *
     * @param Order $order    referenced order
     * @param User  $customer referenced customer
     *
     * @return Bill
     */
    public static function create(Order $order, User $customer = null): Bill
    {
        //Initialization
        if (null === $customer) {
            $customer = $order->getCustomer();
        }

        //Bill creation
        $bill = new Bill();
        $bill->setOrder($order);
        $bill->setCustomer($customer);
        $bill->copyAddress($customer);
        $bill->copyIdentity($customer);
        $bill->copyPrice($order);

        return $bill;
    }
}
