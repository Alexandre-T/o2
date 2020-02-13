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

use Codeception\Util\Locator;

/**
 * Accountant Cest.
 *
 * Test bill accountant use case.
 */
class AccountantCest
{
    /**
     * Accountant tries to list canceled then paid then pending orders.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToCancelNonPendingOrder(AcceptanceTester $you): void
    {
        $you->wantToTest('that accountant cannot cancel pending orders');
        $you->login('accountant');
        $you->areOnPage('/accountant/pay-order/5');
        $you->seeCurrentUrlEquals('/accountant/orders/pending');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Seules les commandes en attente de paiement peuvent être modifiée par le comptable');
        $you->wantToTest('that accountant cannot cancel pending orders');
        $you->areOnPage('/accountant/pay-order/6');
        $you->seeCurrentUrlEquals('/accountant/orders/pending');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Seules les commandes en attente de paiement peuvent être modifiée par le comptable');
    }

    /**
     * Try to credit order.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToCreditOrder(AcceptanceTester $you): void
    {
        $you->wantTo('credit an olsx order');
        $you->login('accountant');
        $you->areOnPage('/accountant/orders/paid');
        $you->seeResponseCodeIsSuccessful();
        $you->click('Créditer le client', Locator::elementAt('//table/tbody/tr', 6)); //44444
        $you->seeResponseCodeIsSuccessful();
        $you->see('Les crédits de cette commande viennent d’être versés au client');
        $you->wantTo('credit a standard order');
        $you->seeResponseCodeIsSuccessful();
        $you->click('Créditer le client', Locator::elementAt('//table/tbody/tr', 1));
        $you->seeResponseCodeIsSuccessful();
        $you->see('Les crédits de cette commande viennent d’être versés au client');
    }

    /**
     * Try to list bills.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToGenerateBill(AcceptanceTester $you): void
    {
        $you->wantTo('list users and create a bill');
        $you->login('accountant');
        $you->areOnPage('/accountant/user?sort=username&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/user?sort=mail&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/user?sort=credit&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/user?sort=nonexistent&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/accountant/user');
        $you->seeResponseCodeIsSuccessful();
        $you->click('2');
        $you->see('Society 17');
        $you->click('Nouvelle facture', 'a.user-18');
        $you->seeResponseCodeIsSuccessful();
        $userId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/accountant/bill/new/'.$userId);
        $you->fillField('Lot(s) de 10 crédits', '1');
        $you->fillField('Lot(s) de 100 crédits', '2');
        $you->fillField('Lot(s) de 500 crédits', '4');
        $you->selectOption('accountant_credit_form[credit]', 1);
        $you->selectOption('accountant_credit_form[method]', 'monetico');
        $you->click('Créer la facture'); //Enregistrement et consultation de la facture
        $you->seeResponseCodeIsSuccessful();
        $billId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/accountant/bill/'.$billId);
        $you->see('La facture a été créé et le client a été crédité à l’instant');
        $you->wantToTest('print page');
        $you->areOnPage('/accountant/bill/print/'.$billId);
        $you->seeResponseCodeIsSuccessful();
    }

    /**
     * Try to list bills.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToListAskedVat(AcceptanceTester $you): void
    {
        $you->wantTo('list vats with a bad sort field');
        $you->login('accountant');
        $you->areOnPage('/accountant/vat?sort=toto');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/vat');
        $you->wantTo('accept a vat');
        $you->click('Accepter', 'a.btn');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Le changement de taux de TVA a été accepté');
        $you->wantTo('reject a vat');
        $you->click('Rejeter', 'a.btn');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Le changement de taux de TVA a été rejeté');
        $you->wantTo('list vats with a bad sort field');
        $you->areOnPage('/accountant/vat?sort=createdAt');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/vat?sort=createdAt');
        $you->areOnPage('/accountant/vat?sort=customers');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/vat?sort=customers');
    }

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
        $you->see('432,00');
        $you->see('Payée');
        $you->click('Consulter', 'a.btn');
        $billId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/accountant/bill/'.$billId);
        $you->seeResponseCodeIsSuccessful();
        $you->click('Factures'); //retour sur le listing
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/bill');
        $you->click('Numéro'); //retour sur le listing
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/bill?sort=number&direction=asc&page=1');
        $you->click('2', '.page-link');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/bill?sort=number&direction=asc&page=2');
    }

    /**
     * Accountant tries to list canceled then paid then pending orders.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToListOrders(AcceptanceTester $you): void
    {
        $you->login('accountant');
        $you->click('Commandes annulées', 'nav.adminbar');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/orders/canceled');
        $you->click('Commandes payées', 'nav.adminbar');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/orders/paid');
        $you->click('Commandes en attente', 'nav.adminbar');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/accountant/orders/pending');
        $you->click('Valider la commande');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Les serveurs OLSX de nos partenaires ne nous ont pas');
        $you->see('Logic exception: you called the 77777 customer');
        $you->click('Valider la commande', Locator::elementAt('//table/tbody/tr',4)); //44444
        $you->seeResponseCodeIsSuccessful();
        $you->see('Cette commande est notée comme payée, le client a été crédité');
        $you->click('Annuler la commande');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Cette commande est notée comme annulée');
    }
}
