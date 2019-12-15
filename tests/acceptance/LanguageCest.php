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
 * Language acceptance test.
 *
 * Acceptance test dedicated to translation.
 */
class LanguageCest
{
    /**
     * Test the home page which shall be in French.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestHomePage(AcceptanceTester $you): void
    {
        $you->wantToTest('I see a french interface');
        $you->amOnPage('/');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Connexion');
        $you->dontSee('Connection');
        $you->wantToTest('I can swap in english interface');
        $you->click('English');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSee('Connexion');
        $you->see('Connection');
        $you->wantToTest('I can swap back to a french interface');
        $you->click('Français');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Connexion');
        $you->dontSee('Connection');
    }

    /**
     * Test the home page with a french user.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestHomePageAsFrenchUser(AcceptanceTester $you): void
    {
        $you->wantToTest('We connect with a french user and we shall return into french language');
        //prepare the test
        $you->areOnPage('/');
        $you->click('English');
        $you->seeResponseCodeIsSuccessful();
        //test is ready, interface is in english mode
        $you->login('all');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();
        $you->seeLink('English');
        $you->dontSeeLink('Français');
        $you->dontSee('Connection');
        $you->wantToTest('The french user can swap in english');
        $you->click('English');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSeeLink('English');
        $you->seeLink('Français');
        $you->dontSeeLink('Déconnexion');
        $you->seeLink('Disconnection');
        $you->click('Français');
        $you->seeResponseCodeIsSuccessful();
        $you->seeLink('English');
        $you->dontSeeLink('Français');
        $you->seeLink('Déconnexion');
        $you->dontSeeLink('Disconnection');
    }
}
