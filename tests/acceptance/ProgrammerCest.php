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
        $you->amOnPage('/programmer/list?sort=createdAt&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->amOnPage('/programmer/list?sort=make&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->amOnPage('/programmer/list?sort=model&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->amOnPage('/programmer/list?sort=deliveredAt&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->amOnPage('/programmer/list?sort=nonexistent&direction=asc&page=1');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/programmer/list');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSee('Vous n’avez pas encore demandé de reprogrammation');
        $you->see('Make1');
    }
}
