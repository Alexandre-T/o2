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
 * Register Cest.
 *
 * Test register use case.
 */
class RegisterCest
{
    /**
     * Try to register.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToRegister(AcceptanceTester $I): void
    {
        $I->wantTo('register.');
        $I->amOnPage('/register');
        $I->fillField('Adresse email', 'new.codeception@example.org');
        $I->fillField('Mot de passe', 'codeception');
        $I->fillField('Confirmation', 'codeception');
        $I->selectOption('app_register[type]', 1);
        $I->fillField('Nom de famille', 'Nom de famille');
        $I->fillField('Adresse', 'rue de Bordeaux');
        $I->fillField('Code postal', '33160');
        $I->fillField('Ville', 'Mérignac');
        $I->selectOption('Pays', 'FR');
        $I->checkOption('J’ai lu les conditions d’utilisation du service');
        $I->click('S’inscrire');
        $I->seeCurrentUrlEquals('/');
        $I->dontSee('Confirmation');
        $I->see('Home page');
    }

    /**
     * Try to send an empty registration.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendAnEmptyRegistration(AcceptanceTester $I): void
    {
        $I->wantTo('send an empty registration.');
        $I->amOnPage('/register');
        $I->click('S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('L’adresse email est obligatoire.');
        $I->see('Le mot de passe est obligatoire.');
        $I->see('L’adresse postale est obligatoire.');
        $I->see('Le code postal est obligatoire.');
        $I->see('La ville est obligatoire.');
        $I->see('Vous devez accepter les CGU pour vous inscrire.');
    }

    /**
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendLongRegistration(AcceptanceTester $I): void
    {
        $codePostal = str_repeat('s', 6);
        $telephone = str_repeat('s', 22);
        $label = str_repeat('s', 33);
        $mail = str_repeat('s', 256);
        $password = str_repeat('s', 4097);

        $I->wantTo('send a long registration.');
        $I->amOnPage('/register');
        $I->fillField('Adresse email', $mail);
        $I->fillField('Mot de passe', $password);
        $I->fillField('Confirmation', $password);
        $I->fillField('Prénom', $label);
        $I->fillField('Nom de famille', $label);
        $I->fillField('Téléphone', $telephone);
        $I->fillField('Numéro de TVA', $label);
        //Société
        $I->fillField('app_register[society]', $label);
        $I->fillField('Adresse', $label);
        $I->fillField('Complément', $label);
        $I->fillField('Code postal', $codePostal);
        $I->fillField('Ville', $label);
        $I->click('S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 5 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 21 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 4096 caractères.');
        $I->see('Cette valeur n\'est pas une adresse email valide.');
    }

    /**
     * Try to send a society without its name.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendSocietyWithoutName(AcceptanceTester $I): void
    {
        $I->wantTo('send register as a society without its name.');
        $I->amOnPage('/register');
        //Société
        $I->selectOption('app_register[type]', 0);
        $I->click('S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Pour les professionnels, le nom de la société est obligatoire.');
    }

    /**
     * Try to send a society without its name.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendPhysicWithoutName(AcceptanceTester $I): void
    {
        $I->wantTo('send register as a physic person without its name.');
        $I->amOnPage('/register');
        //Société
        $I->selectOption('app_register[type]', 1);
        $I->click('S’inscrire');
        $I->seeCurrentUrlEquals('/register');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Pour les particuliers, le nom de famille est obligatoire.');
    }
}
