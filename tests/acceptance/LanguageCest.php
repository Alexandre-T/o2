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
     * Test the home page which shall be in french.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestHomePage(AcceptanceTester $you): void
    {
        $you->wantToTest('I see V-Mod Engineering');
        $you->amOnPage('/');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Connexion');
        $you->dontSee('Connection');
        $you->click('English');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSee('Connexion');
        $you->see('Connection');

        //We connect with a french user and we should return into french language
        $you->login('all');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Connexion');
        $you->seeLink('English');
        $you->dontSeeLink('French');
        $you->dontSee('Connection');
    }
}
