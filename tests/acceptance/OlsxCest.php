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
        //Full test is not available because I cannot mock EvcService in acceptance test.
    }

    /**
     * Try to access to subscription OLSX service when I am already a olsx user.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSubscribeTwice(AcceptanceTester $you): void
    {
        $you->wantTo('access to the OLSX  Subscription form with a olsx account.');
        $you->login('olsx');
        $you->amOnPage('/olsx/register');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('');
        $you->see('Vous êtes déjà inscrit au programme OLSX.');
    }
}
