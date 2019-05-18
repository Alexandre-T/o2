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
        $you->areOnPage('/accountant/bill?sort=amount&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/bill?sort=number&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/bill?sort=customers&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/bill?sort=nonexistent&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/bill');
        $you->seeResponseCodeIsSuccessful();
        $you->see('480,00');
        $you->see('Payée');
        $you->click('Consulter', 'a.btn');
        $you->seeResponseCodeIsSuccessful();
        $billId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/accountant/bill/'.$billId);
        $you->click('Factures'); //retour sur le listing
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/bill');
        $you->click('Numéro'); //retour sur le listing
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/bill?sort=number&direction=asc&page=1');
        $you->click('2', '.page-link');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/bill?sort=number&direction=asc&page=2');
        $you->click('Créditer le client');
        $you->seeResponseCodeIsSuccessful();
        $billId = $you->grabFromCurrentUrl('~highlight=(\d+)~');
        $uri = '/accountant/bill?page=2&sort=number&highlight='.$billId.'&direction=asc&color=success';
        $you->seeCurrentUrlEquals($uri);
        $you->see('Les crédits de cette commande viennent d’être versés au client');

        $you->areOnPage('/accountant/bill/');
    }
}
