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
 * HomeCest class.
 *
 * Acceptance test dedicated to HomeController.
 */
class HomeCest
{
    /**
     * Test the home page.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestHomePage(AcceptanceTester $you): void
    {
        $you->wantToTest('I see V-Mod Engineering');
        $you->amOnPage('/');
        $you->seeResponseCodeIsSuccessful();
        $you->see('V-Mod Engineering');
    }

    /**
     * Test the home page.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToTestTosPage(AcceptanceTester $you): void
    {
        $you->wantToTest('The TOS page is accessible');
        $you->amOnPage('/tos');
        $you->seeResponseCodeIsSuccessful();
    }
}
