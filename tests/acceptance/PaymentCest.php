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
        $you->seeInCurrentUrl('/customer/order-credit');
        $you->see('La procédure de paiement a été interrompue');
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
        $you->seeCurrentUrlEquals('/payment/method-choose');
        $you->click('Poursuivre');
        $you->seeResponseCodeIsSuccessful();
        $you->seeInCurrentUrl('/cgi-bin/webscr?cmd=_express-checkout&token=EC-');
    }
}
