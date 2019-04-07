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
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToTestAdministratorAccess(AcceptanceTester $I): void
    {
        $I->wantTo('be connected as administrator.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'administrator@example.org');
        $I->fillField('Mot de passe', 'administrator');
        $I->click("\u{a0}Se connecter"); //Be careful Se connecter began with ALT+0160 character
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
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Administrator cannot access login page.');
        $I->amOnPage('/login');
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Administrator can access logout page.');
        $I->click('Déconnexion');
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }

    /**
     * Test anonymous user access.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToTestAnonymousAccess(AcceptanceTester $I): void
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

        $I->wantToTest('Anonymous user cannot access secured pages.');
        //Complete it
    }

    /**
     * Test accountant access.
     *
     * @param AcceptanceTester $I the acceptance test
     */
    public function tryToTestAccountantAccess(AcceptanceTester $I): void
    {
        $I->wantTo('be connected as accountant.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'accountant@example.org');
        $I->fillField('Mot de passe', 'accountant');
        $I->click("\u{a0}Se connecter"); //Be careful "Se connecter" began with ALT+0160 character
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
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToTestCustomerAccess(AcceptanceTester $I): void
    {
        $I->wantTo('be connected as customer.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'customer@example.org');
        $I->fillField('Mot de passe', 'customer');
        $I->click("\u{a0}Se connecter"); //Be careful "Se connecter" began with ALT+0160 character
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
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToTestProgrammerAccess(AcceptanceTester $I): void
    {
        $I->wantTo('be connected as programmer.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'programmer@example.org');
        $I->fillField('Mot de passe', 'programmer');
        $I->click("\u{a0}Se connecter"); //Be careful "Se connecter" began with ALT+0160 character
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

    /**
     * Try to register.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToRegister(AcceptanceTester $I): void
    {
        $I->wantTo('register.');
        $I->amOnPage('/register');
        $I->fillField('Adresse email', 'new.codeception@example.org');
        $I->fillField('Mot de passe', 'codeception');
        $I->fillField('Confirmation', 'codeception');
        $I->selectOption('app_register[type]', 1);
        $I->fillField('Nom de famille', 'Nom de famille');
        $I->fillField('Adresse', 'rue de Bordeaux');
        $I->fillField('Code postal', '33160');
        $I->fillField('Ville', 'Mérignac');
        $I->selectOption('Pays', 'FR');
        $I->click(' S’inscrire');
        $I->seeCurrentUrlEquals('/');
        $I->dontSee('Confirmation');
        $I->see('Home page');
    }

    /**
     * Try to send an empty registration.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendAnEmptyRegistration(AcceptanceTester $I): void
    {
        $I->wantTo('send an empty registration.');
        $I->amOnPage('/register');
        $I->click(' S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('L’adresse email est obligatoire.');
        $I->see('Le mot de passe est obligatoire.');
        $I->see('L’adresse postale est obligatoire.');
        $I->see('Le code postal est obligatoire.');
        $I->see('La ville est obligatoire.');
    }

    /**
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendLongRegistration(AcceptanceTester $I): void
    {
        $s6 = str_repeat('s', 6);
        $s22 = str_repeat('s', 22);
        $s33 = str_repeat('s', 33);
        $s256 = str_repeat('s', 256);
        $s4097 = str_repeat('s', 4097);

        $I->wantTo('send a long registration.');
        $I->amOnPage('/register');
        $I->fillField('Adresse email', $s256);
        $I->fillField('Mot de passe', $s4097);
        $I->fillField('Confirmation', $s4097);
        $I->fillField('Prénom', $s33);
        $I->fillField('Nom de famille', $s33);
        $I->fillField('Téléphone', $s22);
        $I->fillField('Numéro de TVA', $s33);
        //Société
        $I->fillField('app_register[society]', $s33);
        $I->fillField('Adresse', $s33);
        $I->fillField('Complément', $s33);
        $I->fillField('Code postal', $s6);
        $I->fillField('Ville', $s33);
        $I->click(' S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 5 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 21 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 4096 caractères.');
        $I->see('Cette valeur n\'est pas une adresse email valide.');
    }

    /**
     * Try to send a society without its name.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendSocietyWithoutName(AcceptanceTester $I): void
    {
        $I->wantTo('send register as a society without its name.');
        $I->amOnPage('/register');
        //Société
        $I->selectOption('app_register[type]', 0);
        $I->click(' S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Pour les professionnels, le nom de la société est obligatoire.');
    }

    /**
     * Try to send a society without its name.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendPhysicWithoutName(AcceptanceTester $I): void
    {
        $I->wantTo('send register as a society without its name.');
        $I->amOnPage('/register');
        //Société
        $I->selectOption('app_register[type]', 1);
        $I->click(' S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Pour les particuliers, le nom de famille est obligatoire.');
    }
}
