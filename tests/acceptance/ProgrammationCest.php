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
 * Customer Cest.
 *
 * Test customer use case.
 */
class ProgrammationCest
{
    /**
     * You try to list your programmation.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToListNonExistentProgrammation(AcceptanceTester $you): void
    {
        $you->wantTo('list an empty programmation');
        $you->login('customer-7');
        $you->amOnPage('/customer/programmation/list');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Vous n’avez pas encore demandé de reprogrammation');
    }
    /**
     * You try to list your programmation.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryListAndShowProgrammation(AcceptanceTester $you): void
    {
        $you->wantTo('list a non-empty programmation');
        $you->login('customer');
        $you->amOnPage('/customer/programmation/list');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSee('Vous n’avez pas encore demandé de reprogrammation');
        $you->see('Make1');
        $you->click('Consulter','a.btn');
        $you->seeResponseCodeIsSuccessful();
        $identifier = $you->grabFromCurrentUrl('~^\/customer\/programmation\/(\d+)$~');
        $you->seeCurrentUrlEquals('/customer/programmation/'.$identifier);
        $you->see('Votre reprogrammation est en cours. Notre équipe travaille dessus');
        $you->click('Déconnexion', 'nav');
        $you->login('programmer');
        $you->click('Fichiers en cours','nav');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/programmer/list');
        $you->click('Télécharger', 'a.btn');
        $you->seeResponseCodeIsSuccessful();
        $programIdentifier = $you->grabFromCurrentUrl('~^/programmer/download-original/(\d+)$~');
        $you->seeCurrentUrlEquals('/programmer/download-original/'.$programIdentifier);
        $you->see('This file is used to be uploaded');
    }
}
