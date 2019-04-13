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
 * Security Cest.
 *
 * Test access by roles.
 */
class SecurityCest
{
    /**
     * Test administrator access.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestAdministratorAccess(AcceptanceTester $you): void
    {
        $you->wantTo('be connected as administrator.');
        $you->login('administrator');

        //We are connected as administrator and are on home page
        $you->wantToTest('administrator see links');
        $you->dontSeeLink('Comptable');
        $you->dontSeeLink('Programmateur');
        $you->dontSeeLink('Inscription');
        $you->dontSeeLink('Connexion');
        $you->seeLink('Administrateur');

        $you->wantToTest('Administrator can access home pages.');
        $you->click('O2 Files');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();

        $you->wantToTest('Administrator cannot access register page.');
        $you->amOnPage('/register');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Administrator cannot access login page.');
        $you->amOnPage('/login');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Administrator can access logout page.');
        $you->click('Déconnexion');
        $you->amOnPage('/');
        $you->seeLink('Connexion');
        $you->seeLink('Inscription');
    }

    /**
     * Test anonymous user access.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestAnonymousAccess(AcceptanceTester $you): void
    {
        $you->wantToTest('Anonymous user can access home page.');
        $you->amOnPage('/');
        $you->seeResponseCodeIsSuccessful();
        $you->wantToTest('Anonymous user can access register page.');
        $you->amOnPage('/register');
        $you->seeResponseCodeIsSuccessful();
        $you->wantToTest('Anonymous user can access logout page and is redirected on home page.');
        $you->amOnPage('/logout');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();

        $you->wantToTest('Anonymous user do not see some links.');
        $you->dontSeeLink('Comptable');
        $you->dontSeeLink('Programmateur');
        $you->dontSeeLink('Administrateur');
        $you->dontSeeLink('Acheter des crédits');

        $you->wantToTest('Anonymous user cannot access profil page');
        $you->amOnPage('/customer/profile');
        $you->seeCurrentUrlEquals('/login');
    }

    /**
     * Test accountant access.
     *
     * @param AcceptanceTester $you the acceptance test
     */
    public function tryToTestAccountantAccess(AcceptanceTester $you): void
    {
        $you->wantTo('be connected as accountant.');
        $you->login('accountant');

        //We are connected as accountant and are on home page
        $you->wantToTest('accountant see links');
        $you->seeLink('Acheter des crédits');
        $you->seeLink('Comptable');
        $you->seeLink('Déconnexion');
        $you->dontSeeLink('Programmateur');
        $you->dontSeeLink('Administrateur');

        $you->wantToTest('Accountant can access home page.');
        $you->click('O2 Files');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();

        $you->wantToTest('Accountant cannot access register page.');
        $you->amOnPage('/register');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Accountant cannot access login page.');
        $you->amOnPage('/login');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Accountant can access logout page.');
        $you->click('Déconnexion');
        $you->amOnPage('/');
        $you->seeLink('Connexion');
        $you->seeLink('Inscription');
    }

    /**
     * Test customer access.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestCustomerAccess(AcceptanceTester $you): void
    {
        $you->wantTo('be connected as customer.');
        $you->login('customer');
        //We are connected as customer and are on home page
        $you->wantToTest('customer see links');
        $you->seeLink('Acheter des crédits');
        $you->seeLink('Déconnexion');
        $you->dontSeeLink('Administrateur');
        $you->dontSeeLink('Comptable');
        $you->dontSeeLink('Programmateur');

        $you->wantToTest('customer can access profil page');
        $you->click('Mon profil');
        $you->seeCurrentUrlEquals('/customer/profile');

        $you->wantToTest('Customer can access home page.');
        $you->click('O2 Files');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();

        $you->wantToTest('Customer cannot access register page.');
        $you->amOnPage('/register');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Customer cannot access lost password page.');
        $you->amOnPage('/password-lost');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Customer cannot access reset password page.');
        $you->amOnPage('/password-reset');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Customer cannot access login page.');
        $you->amOnPage('/login');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Customer can access logout page.');
        $you->click('Déconnexion');
        $you->amOnPage('/');
        $you->seeLink('Connexion');
        $you->seeLink('Inscription');
    }

    /**
     * Test programmer access.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestProgrammerAccess(AcceptanceTester $you): void
    {
        $you->wantTo('be connected as programmer.');
        $you->login('programmer');

        //We are connected as programmer and are on home page
        $you->wantToTest('programmer see links');
        $you->seeLink('Acheter des crédits');
        $you->seeLink('Programmateur');
        $you->seeLink('Déconnexion');
        $you->dontSeeLink('Comptable');
        $you->dontSeeLink('Administrateur');

        $you->wantToTest('Programmer can access home page.');
        $you->click('O2 Files');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();

        $you->wantToTest('Programmer cannot access register page.');
        $you->amOnPage('/register');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Programmer cannot access login page.');
        $you->amOnPage('/login');
        $you->seeCurrentUrlEquals('/');

        $you->wantToTest('Programmer can access logout page.');
        $you->click('Déconnexion');
        $you->amOnPage('/');
        $you->seeLink('Connexion');
        $you->seeLink('Inscription');
    }
}
