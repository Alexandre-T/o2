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
 * OLSX Cest.
 *
 * Test process of the OLSX services.
 */
class OlsxCest
{
    /**
     * Try to access to subscription OLSX service without login.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToGoOnSecuredPage(AcceptanceTester $you): void
    {
        $you->wantTo('access to the OLSX  Subscription form without login.');
        $you->amOnPage('/olsx/register');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/login');
    }

    /**
     * Try to access to subscription OLSX service when I am already a olsx user.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSubscribe(AcceptanceTester $you): void
    {
        $you->wantTo('access to the OLSX  Subscription form.');
        $you->login('customer');
        $you->amOnPage('/olsx/register');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/register');
        $you->wantToTest('vulnerability. toto can explode the website.');
        $you->fillField('Code client OLSX', 'toto');
        $you->click("Inscrivez-vous\u{a0}!");
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/register');
        $you->see('Erreur Cette valeur n\'est pas valide.');
        $you->wantToTest('non-existent customer. 11111 is a non-existent customer.');
        $you->fillField('Code client OLSX', '11111');
        $you->click("Inscrivez-vous\u{a0}!");
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/register');
        $you->see('Erreur Ce code utilisateur n’est pas reconnu par les services OLSX.');
        $you->wantToTest('Network exception. 55555 is not a valid customer.');
        $you->fillField('Code client OLSX', '55555');
        $you->click("Inscrivez-vous\u{a0}!");
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/register');
        $you->see('Nous ne pouvons pas vérifier votre code OLSX pour le moment. Veuillez réessayer ultérieurement.');
        $you->wantToTest('Credential exception. 66666 is not a valid customer.');
        $you->fillField('Code client OLSX', '66666');
        $you->click("Inscrivez-vous\u{a0}!");
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/register');
        $you->see('Nous ne pouvons pas vérifier votre code OLSX pour le moment. Veuillez réessayer ultérieurement.');
        $you->wantToTest('Logical exception. 77777 is not a valid customer.');
        $you->fillField('Code client OLSX', '77777');
        $you->click("Inscrivez-vous\u{a0}!");
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/register');
        $you->see('Nous ne pouvons pas vérifier votre code OLSX pour le moment. Veuillez réessayer ultérieurement.');
        $you->wantToTest('A valid registration.');
        $you->fillField('Code client OLSX', '44444');
        $you->click("Inscrivez-vous\u{a0}!");
        $you->seeResponseCodeIsSuccessful();
        $you->see('Votre inscription au programme OLSX a bien été prise en compte');
        $you->seeCurrentUrlEquals('');
    }

    /**
     * Try to access to subscription OLSX service when I am already a olsx user.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSubscribeTwice(AcceptanceTester $you): void
    {
        $you->wantTo('access to the OLSX  Subscription form with a olsx account.');
        $you->login('olsx1');
        $you->amOnPage('/olsx/register');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('');
        $you->see('Vous êtes déjà inscrit au programme OLSX.');
    }

    /**
     * Try to list OLSX registering users then try to manipulate them.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToListRegisteringUsers(AcceptanceTester $you): void
    {
        $you->wantTo('show the list of registering users.');
        $you->login('accountant');
        $you->amOnPage('/olsx/registering');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/registering');
        $you->see('77777');
        $you->see('66666');
        $you->see('55555');
        $you->see('44444');
        $you->see('33333');
        $you->see('22222');
        $you->see('11111');
        $you->wantTo('test the 11111 customer');
        $you->click('Consulter', Locator::elementAt('//table/tbody/tr', 7));
        $you->see('OLSX-registering-1');
        $you->see('Compte OLSX inexistant');
        $you->see('Non Personnel');
        $you->see('Aucun');
        $you->see('Client simple');
        $you->wantTo('test the 22222 customer');
        $you->amOnPage('/olsx/registering');
        $you->click('Consulter',Locator::elementAt('//table/tbody/tr', 6));
        $you->see('OLSX-registering-2');
        $you->see('Compte OLSX existant');
        $you->see('Non Personnel');
        $you->see('Aucun');
        $you->see('Client simple');
        $you->wantTo('create a personal account for the 22222 customer');
        $identifier = $you->grabFromCurrentUrl('~^\/olsx\/show\/(\d+)$~');
        $you->click('Accorder un compte personnel');
        $you->seeCurrentUrlEquals('/olsx/show/' . $identifier);
        $you->see('un compte personnel vient d’être ouvert');
        $you->wantTo('test the 33333 customer');
        $you->amOnPage('/olsx/registering');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/registering');
        $you->click('Consulter',Locator::elementAt('//table/tbody/tr', 5));
        $you->see('OLSX-registering-3');
        $you->see('Compte OLSX existant');
        $you->see('Personnel');
        $you->see('42 crédits');
        $you->see('Client simple');
        $you->wantTo('open OLSX service to the 33333 customer');
        $identifier = $you->grabFromCurrentUrl('~^\/olsx\/show\/(\d+)$~');
        $you->click('Activer le compte OLSX');
        $you->seeCurrentUrlEquals('/olsx/show/' . $identifier);
        $you->see('Ce client a désormais la possibilité de commander des crédits OLSX');
        $you->wantTo('close OLSX service to the 33333 customer');
        $you->click('Désactiver le compte OLSX');
        $you->seeCurrentUrlEquals('/olsx/show/' . $identifier);
        $you->see('Ce client n’a plus la possibilité de commander des crédits OLSX');
        $you->wantTo('test the 55555 customer');
        $you->amOnPage('/olsx/registering');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/registering');
        $you->click('Consulter',Locator::elementAt('//table/tbody/tr', 3));
        $you->see('Le service OLSX de notre partenaire est actuellement injoignable');
        $you->seeCurrentUrlEquals('');
        $you->wantTo('test the 66666 customer');
        $you->amOnPage('/olsx/registering');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/registering');
        $you->click('Consulter',Locator::elementAt('//table/tbody/tr', 2));
        $you->seeCurrentUrlEquals('');
        $you->see('Erreur interne de configuration pour le service OLSX');
        $you->wantTo('test the 77777 customer');
        $you->amOnPage('/olsx/registering');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/olsx/registering');
        $you->click('Consulter',Locator::elementAt('//table/tbody/tr', 1));
        $you->seeCurrentUrlEquals('/');
        $you->see('Le service OLSX de notre partenaire nous a retourné une réponse non attendue');
    }
}
