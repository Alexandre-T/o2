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
 * Bill Cest.
 *
 * Test bill customer use case.
 */
class BillCest
{
    /**
     * Try to list bills.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToListBills(AcceptanceTester $you): void
    {
        $you->wantTo('list bills and show an accessible one');
        $you->login('customer-4');
        $you->amOnPage('/customer/bill/list');
        $you->seeResponseCodeIsSuccessful();
        $you->see('480');
        $you->see('Payée');
        $you->click('Consulter', 'a.btn');
        $you->seeResponseCodeIsSuccessful();
        $billId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals("/customer/bill/$billId");

        $you->wantTo('show a non-accessible one');
        $you->login('customer-3');
        $you->amOnPage("/customer/bill/$billId");
        $you->canSeeResponseCodeIsClientError();
        $you->canSeeResponseCodeIs(403);
    }
}
