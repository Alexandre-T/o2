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
        $you->wantTo('show the list of new users.');
        $you->login('olsx1');
        $you->amOnPage('/olsx/register');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('');
        $you->see('Vous êtes déjà inscrit au programme OLSX.');
    }
}
