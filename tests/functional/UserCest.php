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
 * Home page functional test.
 */
class UserCest
{
    /**
     * Try to test home page.
     *
     * @param FunctionalTester $I tester
     */
    public function tryToTest(FunctionalTester $I): void
    {
        $I->wantToTest('I see title');
        $I->amOnPage('/');
        $I->see('O2 files');
    }
}
