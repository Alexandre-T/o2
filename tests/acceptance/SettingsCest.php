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
        $you->click('Modifier ce paramètre', 'tr.legacy-rcs');
        $settingId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/administration/settings/'.$settingId.'/edit');
        $you->fillField('app_settings[value]', 'toto');
        $you->click('Éditer', 'button.btn');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/administration/settings/');
        $you->see('toto');
    }

    /**
     * Try to update date settings.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToUpdateDateSettings(AcceptanceTester $you): void
    {
        $you->wantTo('list all settings');
        $you->login('administrator');
        $you->areOnPage('/administration/settings');
        $you->seeResponseCodeIsSuccessful();
        $you->click('Modifier ce paramètre', 'tr.service-until');
        $settingId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/administration/settings/'.$settingId.'/edit');
        $you->see('Service fermé jusqu’au');
        $you->selectOption('app_settings[value][day]', '21');
        $you->selectOption('app_settings[value][month]', '12');
        $you->selectOption('app_settings[value][year]', '2020');
        $you->click('Éditer', 'button.btn');
        $you->seeResponseCodeIsSuccessful();
    }

    /**
     * Try to update status settings.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToUpdateStatusSettings(AcceptanceTester $you): void
    {
        $you->wantTo('list all settings');
        $you->login('administrator');
        $you->areOnPage('/administration/settings');
        $you->seeResponseCodeIsSuccessful();
        $you->click('Modifier ce paramètre', 'tr.service-status');
        $settingId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/administration/settings/'.$settingId.'/edit');
        $you->see('Statut du service de reprogrammation');
        $you->selectOption('app_settings[value]', '2');
        $you->click('Éditer', 'button.btn');
        $you->seeResponseCodeIsSuccessful();
        $you->see('En vacances');
    }

    /**
     * Try to update welcome message.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToUpdateWelcomeMessage(AcceptanceTester $you): void
    {
        $you->wantTo('try to update welcome message');
        $you->login('administrator');
        $you->areOnPage('/administration/settings/welcome-message');
        $you->seeResponseCodeIsSuccessful();
        $you->fillField('welcome_form[french]', 'Nouveau message de bienvenue');
        $you->fillField('welcome_form[english]', 'New welcome message');
        $you->click('Éditer', 'button.btn');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Le paramètre « welcome-fr » a été mis à jour');
        $you->see('Le paramètre « welcome-en » a été mis à jour');

        $you->wantTo('verify the welcome message');
        $you->areOnPage('/');
        $you->see('Nouveau message de bienvenue');
    }
}
