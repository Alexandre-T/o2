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
 * Payment Cest.
 *
 * Test payment use case.
 */
class PaymentCest
{
    /*
     * Key of payment provided by SQL or Travis.
     */
    public const KEY = 'myManualValue-';

    /**
     * Try to cancel a payment.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToCancelPayment(AcceptanceTester $you): void
    {
        $you->wantTo('simulate a cancel payment');
        $you->login('customer-7');
        $you->amOnPage('/payment/cancel/4');
        $you->seeResponseCodeIsSuccessful();
        $you->seeInCurrentUrl('/payment/method-choose/4');
        $you->see('La procédure de paiement a été interrompue');
    }

    /**
     * Try to go to the analyse/done page directly.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToGoToDoneDirectly(AcceptanceTester $you): void
    {
        $you->wantTo('go to the done page manually anonymously');
        $you->areOnPage('/payment/done');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Cette commande en attente de paiement n’existe pas ou a été payée.', '.alert-danger');
        $you->seeCurrentUrlEquals('');
    }

    /**
     * Try to go to the analyse/done page directly.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToGoToDoneDirectlyAsCustomer(AcceptanceTester $you): void
    {
        $you->wantTo('go to the done page manually as a customer');
        $you->login('customer');
        $you->areOnPage('/payment/analyse');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Cette commande en attente de paiement n’existe pas ou a été payée.', '.alert-danger');
        $you->seeCurrentUrlEquals('/customer/order-credit');
    }

    /**
     * Try to order some credit.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToOrderCredit(AcceptanceTester $you): void
    {
        $you->wantTo('connect as customer and try to order some credits');
        $you->login('customer');
        $you->amOnPage('/customer/order-credit');
        $you->fillField('Lot(s) de 10 crédits', 4);
        $you->fillField('Lot(s) de 100 crédits', 1);
        $you->fillField('Lot(s) de 500 crédits', 2);
        $you->click('Enregistrer votre commande');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/payment/method-choose/1');
        $you->click('Poursuivre');
        $you->seeResponseCodeIsSuccessful();
        //This test cannot be realized on travis because of non-existent api on travis for paypal
        //$you->seeInCurrentUrl('/cgi-bin/webscr?cmd=_express-checkout&token=EC-');
    }
}
