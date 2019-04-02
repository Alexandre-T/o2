<?php

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
     * @param AcceptanceTester $I
     */
    public function tryToTestAdministratorAccess(AcceptanceTester $I)
    {
        $I->wantTo('be connected as administrator.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'administrator@example.org');
        $I->fillField('Mot de passe', 'administrator');
        $I->click(' Se connecter'); //Be careful Se connecter began with ALT+0160 character
        $I->seeCookie('PHPSESSID');
        $I->seeCurrentUrlEquals('/');

        //We are connected as administrator and are on home page
        $I->wantToTest('administrator see links');
        $I->dontSeeLink('Comptable');
        $I->dontSeeLink('Programmateur');
        $I->dontSeeLink('Inscription');
        $I->dontSeeLink('Connexion');
        $I->seeLink('Administrateur');

        $I->wantToTest('Administrator can access home pages.');
        $I->click('O2 Files');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Administrator cannot access register page.');
        $I->amOnPage('/register');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Administrator cannot access login page.');
        $I->amOnPage('/login');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Administrator can access logout page.');
        $I->click('Déconnexion');
        //$I->seeResponseCodeIsRedirection();
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }

    /**
     * Test anonymous user access.
     *
     * @param AcceptanceTester $I
     */
    public function tryToTestAnonymousAccess(AcceptanceTester $I)
    {
        $I->wantToTest('Anonymous user can access home page.');
        $I->amOnPage('/');
        $I->seeResponseCodeIsSuccessful();
        $I->wantToTest('Anonymous user can access register page.');
        $I->amOnPage('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->wantToTest('Anonymous user can access logout page and is redirected on home page.');
        $I->amOnPage('/logout');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Anonymous user do not see some links.');
        $I->dontSeeLink('Comptable');
        $I->dontSeeLink('Programmateur');
        $I->dontSeeLink('Administrateur');
        $I->dontSeeLink('Acheter des crédits');

        $I->wantToTest('Anonymous user cannot access contact pages.');
        //$I->amOnPage('/person');
        //$I->seeResponseCodeIsSuccessful();
        //$I->seeCurrentUrlEquals('/login');
    }

    /**
     * Test accountant access.
     *
     * @param AcceptanceTester $I
     */
    public function tryToTestAccountantAccess(AcceptanceTester $I)
    {
        $I->wantTo('be connected as accountant.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'accountant@example.org');
        $I->fillField('Mot de passe', 'accountant');
        $I->click(' Se connecter'); //Be careful "Se connecter" began with ALT+0160 character
        $I->seeCookie('PHPSESSID');
        $I->seeCurrentUrlEquals('/');

        //We are connected as accountant and are on home page
        $I->wantToTest('accountant see links');
        $I->seeLink('Acheter des crédits');
        $I->seeLink('Comptable');
        $I->seeLink('Déconnexion');
        $I->dontSeeLink('Programmateur');
        $I->dontSeeLink('Administrateur');

        $I->wantToTest('Accountant can access home page.');
        $I->click('O2 Files');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Accountant cannot access register page.');
        $I->amOnPage('/register');
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Accountant cannot access login page.');
        $I->amOnPage('/login');
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Accountant can access logout page.');
        $I->click('Déconnexion');
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }
    
    /**
     * Test customer access.
     *
     * @param AcceptanceTester $I
     */
    public function tryToTestCustomerAccess(AcceptanceTester $I)
    {
        $I->wantTo('be connected as customer.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'customer@example.org');
        $I->fillField('Mot de passe', 'customer');
        $I->click(' Se connecter'); //Be careful "Se connecter" began with ALT+0160 character
        $I->seeCookie('PHPSESSID');
        $I->seeCurrentUrlEquals('/');

        //We are connected as customer and are on home page
        $I->wantToTest('customer see links');
        $I->seeLink('Acheter des crédits');
        $I->seeLink('Déconnexion');
        $I->dontSeeLink('Administrateur');
        $I->dontSeeLink('Comptable');
        $I->dontSeeLink('Programmateur');

        $I->wantToTest('Customer can access home page.');
        $I->click('O2 Files');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Customer cannot access register page.');
        $I->amOnPage('/register');
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Customer cannot access login page.');
        $I->amOnPage('/login');
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Customer can access logout page.');
        $I->click('Déconnexion');
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }

    /**
     * Test programmer access.
     *
     * @param AcceptanceTester $I
     */
    public function tryToTestProgrammerAccess(AcceptanceTester $I)
    {
        $I->wantTo('be connected as programmer.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'programmer@example.org');
        $I->fillField('Mot de passe', 'programmer');
        $I->click(' Se connecter'); //Be careful "Se connecter" began with ALT+0160 character
        $I->seeCookie('PHPSESSID');
        $I->seeCurrentUrlEquals('/');

        //We are connected as programmer and are on home page
        $I->wantToTest('programmer see links');
        $I->seeLink('Acheter des crédits');
        $I->seeLink('Programmateur');
        $I->seeLink('Déconnexion');
        $I->dontSeeLink('Comptable');
        $I->dontSeeLink('Administrateur');

        $I->wantToTest('Programmer can access home page.');
        $I->click('O2 Files');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Programmer cannot access register page.');
        $I->amOnPage('/register');
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Programmer cannot access login page.');
        $I->amOnPage('/login');
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Programmer can access logout page.');
        $I->click('Déconnexion');
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }
}
