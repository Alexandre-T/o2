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
 * Cic Cest.
 *
 * Test cic returns.
 */
class CicCest
{
    /**
     * Try to simulate some cic notifications.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToSimulateCicNotification(AcceptanceTester $you): void
    {
        $you->wantTo('simulate a cic return without data');
        $you->areOnPage('/retour-cic');
        $you->see('version=2');
        $you->see('cdr=1');
        $you->wantTo('simulate a cic return with a wrong TPE');
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $you->areOnPage('retour-cic?TPE=5555555&date=05%2f12%2f2006_a_11%3a55%3a23&montant=62.75EUR&reference=ABERTYP00145&MAC=e4359a2c18d86cf2e4b0e646016c202e89947b04&texte-libre=LeTexteLibre&code-retour=paiement&cvx=oui&vld=1208&brand=VI&status3ds=1&numauto=010101&originecb=FRA&bincb=010101&hpancb=74E94B03C22D786E0F2C2CADBFC1C00B004B7C45&ipclient=127.0.0.1&originetr=FRA&veres=Y&pares=Y&authentification=ewoJInN0YXR1cyIgOiAiYXV0aGVudGljYXRlZCIsCgkicHJvdG9jb2wiIDogIjNEU2VjdXJlIiwKCSJ2ZXJzaW9uIiAiMi4xLjAiLAoJImRldGFpbHMiIDogCgl7CgkJImxpYWJpbGl0eVNoaWZ0IiA6ICJZIiwKCQkiQVJlcyIgOiAiQyIsCgkJIkNSZXMiIDogIlkiLAoJCSJtZXJjaGFudFByZWZlcmVuY2UiIDogIm5vX3ByZWZlcmVuY2UiLAoJCSJ0cmFuc2FjdGlvbklEIiA6ICI1NTViZDlkOS0xY2YxLTRiYTgtYjM3Yy0xYTk2YmM4YjYwM2EiLAoJCSJhdXRoZW50aWNhdGlvblZhbHVlIiA6ICJjbUp2ZDBJNFNIazNVVFJrWWtGU1EzRllZM1U9IgoJfQp9');
        $you->see('version=2');
        $you->see('cdr=1');
        $you->wantTo('simulate a cic return with a wrong MAC');
        $you->areOnPage('retour-cic?TPE=1234567&date=05%2f12%2f2006_a_11%3a55%3a23&montant=62.75EUR&reference=ABERTYP00145&MAC=e4359a2c18d86cf2e4b0e646016c202e89947b04&texte-libre=LeTexteLibre&code-retour=paiement&cvx=oui&vld=1208&brand=VI&status3ds=1&numauto=010101&originecb=FRA&bincb=010101&hpancb=74E94B03C22D786E0F2C2CADBFC1C00B004B7C45&ipclient=127.0.0.1&originetr=FRA&veres=Y&pares=Y&authentification=ewoJInN0YXR1cyIgOiAiYXV0aGVudGljYXRlZCIsCgkicHJvdG9jb2wiIDogIjNEU2VjdXJlIiwKCSJ2ZXJzaW9uIiAiMi4xLjAiLAoJImRldGFpbHMiIDogCgl7CgkJImxpYWJpbGl0eVNoaWZ0IiA6ICJZIiwKCQkiQVJlcyIgOiAiQyIsCgkJIkNSZXMiIDogIlkiLAoJCSJtZXJjaGFudFByZWZlcmVuY2UiIDogIm5vX3ByZWZlcmVuY2UiLAoJCSJ0cmFuc2FjdGlvbklEIiA6ICI1NTViZDlkOS0xY2YxLTRiYTgtYjM3Yy0xYTk2YmM4YjYwM2EiLAoJCSJhdXRoZW50aWNhdGlvblZhbHVlIiA6ICJjbUp2ZDBJNFNIazNVVFJrWWtGU1EzRllZM1U9IgoJfQp9');
        // phpcs:enable
        $you->see('version=2');
        $you->see('cdr=1');
    }
}