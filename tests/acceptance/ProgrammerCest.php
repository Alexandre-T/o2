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

use App\Model\ServiceStatusInterface;

/**
 * Programmer Cest.
 *
 * Test customer use case.
 */
class ProgrammerCest
{
    /**
     * You try to list all programmation.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryListProgrammation(AcceptanceTester $you): void
    {
        $you->wantTo('list a non-empty programmation');
        $you->login('programmer');
        $you->areOnPage('/programmer/list?sort=createdAt&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/programmer/list?sort=make&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/programmer/list?sort=model&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/programmer/list?sort=deliveredAt&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->areOnPage('/programmer/list?sort=nonexistent&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/programmer/list');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSee('Vous n’avez pas encore demandé de reprogrammation');
        $you->see('Make1');
    }

    /**
     * Test the change status use case.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToChangeStatus(AcceptanceTester $you): void
    {
        $you->wantTo('open the service');
        $you->login('programmer');
        $you->areOnPage('/programmer/status/open');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/programmer/list');
        $you->see('Le service de reprogrammation est désormais ouvert.');
        $you->click('Ouvert');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/');
        $you->see('Le service de reprogrammation est désormais fermé.');
        $you->click('Fermé');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/programmer/list');
        $you->see('Le service de reprogrammation est désormais ouvert.');
        $you->click('Statut du service');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/programmer/status');
        $you->selectOption('service_status_form[status]', ServiceStatusInterface::VACANCY);
        $you->selectOption('service_status_form[endAt][day]', 1);
        $you->selectOption('service_status_form[endAt][month]', 1);
        $you->selectOption('service_status_form[endAt][year]', 2024);
        //Click on button with submit type does not work in test.
        $you->submitForm('form', [
            'service_status_form[status]' => ServiceStatusInterface::VACANCY,
            'service_status_form[endAt][day]' => 1,
            'service_status_form[endAt][month]' => 1,
            'service_status_form[endAt][year]' => 2024,
        ]);
        $you->seeResponseCodeIsSuccessful();

        //The code above does not work because form seems to be not valid
        $you->seeCurrentUrlEquals('/');
        $you->see('Le statut du service de reprogrammation a été mis à jour.');
        $you->see('En vacances');
    }
}
