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
 * Settings Cest.
 *
 * Test settings use case.
 */
class SettingsCest
{
    /**
     * Try to list and update settings.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToListAndUpdateSettings(AcceptanceTester $you): void
    {
        $you->wantTo('list all settings');
        $you->login('administrator');
        $you->areOnPage('/administration/settings/?sort=code&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/administration/settings/?sort=nonexistent&direction=desc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/administration/settings/');
        $you->dontSee('settings.'); //all is translated
        $you->click('Paramètre'); // change order
        $you->seeResponseCodeIsSuccessful();
        $you->click('Modifier ce paramètre');
        $settingId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/administration/settings/'.$settingId.'/edit');
        $you->fillField('app_settings[value]', 'toto');
        $you->click('Éditer', 'button.btn');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/administration/settings/');
        $you->see('toto');
    }
}
