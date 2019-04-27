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

namespace App\Tests;

/**
 * Accountant Cest.
 *
 * Test bill accountant use case.
 */
class AccountantCest
{
    /**
     * Try to list bills.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToListBills(AcceptanceTester $you): void
    {
        $you->wantTo('list bills and show an accessible one');
        $you->login('accountant');
        $you->amOnPage('/accountant/bill');
        $you->seeResponseCodeIsSuccessful();
        $you->see('480,00');
        $you->see('Payée');
        $you->click('Consulter', 'a.btn');
        $you->seeResponseCodeIsSuccessful();
        $billId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/accountant/bill/'.$billId);
    }
}
