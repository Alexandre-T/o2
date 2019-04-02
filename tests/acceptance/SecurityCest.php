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
        $I->dontSeeLink('Contacts');
        $I->dontSeeLink('Organisations');
        $I->seeLink('Utilisateurs');

        $I->wantToTest('Administrator can access home pages.');
        $I->click(' Accueil');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Administrator can access admin pages.');
        $I->click('Utilisateurs');
        $I->seeCurrentUrlEquals('/administration/user');
        $I->seeResponseCodeIsSuccessful();
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/administration/user/new');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Administrator cannot access contact pages.');
        $I->amOnPage('/person');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/person/new');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/person/service/organization.json');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Administrator cannot access organization pages.');
        $I->amOnPage('/organization');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/organization/new');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Administrator cannot access search page.');
        $I->amOnPage('/search/index');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/search/mail');
        $I->seeResponseCodeIs(403);

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
        $I->dontSeeLink('Contacts');
        $I->dontSeeLink('Organisations');
        $I->dontSeeLink('Utilisateurs');

        $I->wantToTest('Anonymous user cannot access contact pages.');
        $I->amOnPage('/person');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');
        $I->amOnPage('/person/new');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');
        $I->amOnPage('/person/service/organization.json');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');

        $I->wantToTest('Anonymous user cannot access organization pages.');
        $I->amOnPage('/organization');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');
        $I->amOnPage('/organization/new');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');

        $I->wantToTest('Anonymous user cannot access search pages.');
        $I->amOnPage('/search/index');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');
        $I->amOnPage('/search/mail');
        $I->seeCurrentUrlEquals('/login');

        $I->wantToTest('Anonymous user cannot access admin pages.');
        $I->amOnPage('/administration/user');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');
        $I->amOnPage('/administration/user/new');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');
    }

    /**
     * Test organiser access.
     *
     * @param AcceptanceTester $I
     */
    public function tryToTestOrganiserAccess(AcceptanceTester $I)
    {
        $I->wantTo('be connected as organiser.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'organiser@example.org');
        $I->fillField('Mot de passe', 'organiser');
        $I->click(' Se connecter'); //Be careful Se connecter began with ALT+0160 character
        $I->seeCookie('PHPSESSID');
        $I->seeCurrentUrlEquals('/');

        //We are connected as administrator and are on home page
        $I->wantToTest('organiser see links');
        $I->seeLink('Contacts');
        $I->seeLink('Organisations');
        $I->dontSeeLink('Utilisateurs');

        $I->wantToTest('Organiser can access home page.');
        $I->click(' Accueil');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Organiser can access contact pages.');
        $I->click('Contacts');
        $I->seeCurrentUrlEquals('/person');
        $I->seeResponseCodeIsSuccessful();
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/person/new');
        $I->seeResponseCodeIsSuccessful();
        $I->amOnPage('/person/service/organization.json');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Organiser can access organization pages.');
        $I->amOnPage('/');
        $I->click('Organisations');
        $I->seeResponseCodeIsSuccessful();
        $I->click('Créer');
        $I->amOnPage('/organization/new');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Organiser can access search page.');
        $I->amOnPage('/search/index');
        $I->seeResponseCodeIsSuccessful();
        $I->amOnPage('/search/mail');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Organiser cannot access admin pages.');
        $I->amOnPage('/administration/user');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/administration/user/new');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Organiser cannot access register page.');
        $I->amOnPage('/register');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Organiser cannot access login page.');
        $I->amOnPage('/login');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Organiser can access logout page.');
        $I->click('Déconnexion');
        //$I->seeResponseCodeIsRedirection();
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }

    /**
     * Test banned access.
     *
     * @param AcceptanceTester $I
     */
    public function tryToTestBannedAccess(AcceptanceTester $I)
    {
        $I->wantTo('be connected as banned user.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'user@example.org');
        $I->fillField('Mot de passe', 'user');
        $I->click(' Se connecter'); //Be careful Se connecter began with ALT+0160 character
        $I->seeCookie('PHPSESSID');
        $I->seeCurrentUrlEquals('/');

        //We are connected as administrator and are on home page
        $I->wantToTest('users see links');
        $I->dontSeeLink('Contacts');
        $I->dontSeeLink('Organisations');
        $I->dontSeeLink('Utilisateurs');

        $I->wantToTest('Banned user can access home page.');
        $I->click(' Accueil');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Banned user cannot access contact pages.');
        $I->amOnPage('/person');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/person/new');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/person/service/organization.json');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Banned user cannot access organization pages.');
        $I->amOnPage('/organization');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/organization/new');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Administrator cannot access search page.');
        $I->amOnPage('/search/index');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/search/mail');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Banned user cannot access admin pages.');
        $I->amOnPage('/administration/user');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/administration/user/new');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Banned user cannot access register page.');
        $I->amOnPage('/register');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Banned user cannot access login page.');
        $I->amOnPage('/login');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Banned user can access logout page.');
        $I->click('Déconnexion');
        //$I->seeResponseCodeIsRedirection();
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }

    /**
     * Test reader access.
     *
     * @param AcceptanceTester $I
     */
    public function tryToTestReaderAccess(AcceptanceTester $I)
    {
        $I->wantTo('be connected as reader.');
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'reader@example.org');
        $I->fillField('Mot de passe', 'reader');
        $I->click(' Se connecter'); //Be careful Se connecter began with ALT+0160 character
        $I->seeCookie('PHPSESSID');
        $I->seeCurrentUrlEquals('/');

        //We are connected as administrator and are on home page
        $I->wantToTest('reader see links');
        $I->seeLink('Contacts');
        $I->seeLink('Organisations');
        $I->dontSeeLink('Utilisateurs');

        $I->wantToTest('Reader can access home page.');
        $I->click(' Accueil');
        $I->seeCurrentUrlEquals('/');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Reader can access contact pages.');
        $I->click('Contacts');
        $I->seeCurrentUrlEquals('/person');
        $I->seeResponseCodeIsSuccessful();
        $I->dontSeeLink('Créer');
        $I->dontSeeLink('Éditer');
        $I->seeLink('Consulter');
        $I->click('Consulter', 'a.btn');
        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/person/$id");
        $I->dontSeeLink('Éditer');
        $I->dontSeeLink('Supprimer');
        $I->amOnPage('/person/service/organization.json');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/person/new');
        $I->seeResponseCodeIs(403);
        $I->amOnPage("/person/$id/edit");
        $I->seeResponseCodeIs(403);
//        $I->deleteOnPage("/person/$id");
//        $I->seeResponseCodeIs(403);

        $I->wantToTest('Reader can access organization pages.');
        $I->amOnPage('/');
        $I->click('Organisations');
        $I->seeResponseCodeIsSuccessful();
        $I->dontSeeLink('Créer');
        $I->dontSeeLink('Éditer');
        $I->seeLink('Consulter');
        $I->click('Consulter', 'a.btn');
        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/organization/$id");
        $I->dontSeeLink('Éditer');
        $I->dontSeeLink('Supprimer');
        $I->amOnPage('/organization/new');
        $I->seeResponseCodeIs(403);
        $I->amOnPage("/organization/$id/edit");
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Reader can access search page.');
        $I->amOnPage('/search/index');
        $I->seeResponseCodeIsSuccessful();
        $I->amOnPage('/search/mail');
        $I->seeResponseCodeIsSuccessful();

        $I->wantToTest('Reader cannot access admin pages.');
        $I->amOnPage('/administration/user');
        $I->seeResponseCodeIs(403);
        $I->amOnPage('/administration/user/new');
        $I->seeResponseCodeIs(403);

        $I->wantToTest('Reader cannot access register page.');
        $I->amOnPage('/register');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Reader cannot access login page.');
        $I->amOnPage('/login');
        //$I->seeResponseCodeIsRedirection();
        $I->seeCurrentUrlEquals('/');

        $I->wantToTest('Reader can access logout page.');
        $I->click('Déconnexion');
        //$I->seeResponseCodeIsRedirection();
        $I->amOnPage('/');
        $I->seeLink('Connexion');
        $I->seeLink('Inscription');
    }
}
