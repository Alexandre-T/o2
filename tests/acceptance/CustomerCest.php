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
class CustomerCest
{
    /**
     * @param AcceptanceTester $I the acceptance tester
     */
    public function login(AcceptanceTester $I): void
    {
        $I->amOnPage('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->fillField('Adresse email', 'customer@example.org');
        $I->fillField('Mot de passe', 'customer');
        $I->click("\u{a0}Se connecter"); //Be careful "Se connecter" began with ALT+0160 character
        $I->seeResponseCodeIsSuccessful();
    }

    /**
     * Try to update customer.
     *
     * @before login
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToUpdateCustomer(AcceptanceTester $I): void
    {
        $I->wantTo('update customer.');
        $I->amOnPage('/customer/profile');
        $I->selectOption('app_profile[type]', 1);
        $I->fillField('Nom de famille', 'Nom de famille');
        $I->fillField('Adresse', 'rue de Bordeaux');
        $I->fillField('Code postal', '33160');
        $I->fillField('Ville', 'Mérignac');
        $I->selectOption('Pays', 'FR');
        $I->click('Enregistrer les modifications');
        $I->seeCurrentUrlEquals('/customer/profile');
        $I->seeResponseCodeIsSuccessful();
        $I->dontSee("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $I->see('Votre profil a correctement été mis à jour.');
    }

    /**
     * Try to send an empty form.
     *
     * @before login
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendAnEmptyForm(AcceptanceTester $I): void
    {
        $I->wantTo('send an empty form.');
        $I->amOnPage('/customer/profile');
        $I->fillField('Nom de famille', '');
        $I->fillField('Adresse', '');
        $I->fillField('Code postal', '');
        $I->fillField('Ville', '');
        $I->click('Enregistrer les modifications');
        $I->seeCurrentUrlEquals('/customer/profile');
        $I->seeResponseCodeIsSuccessful();
        $I->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $I->dontSee('Votre profil a correctement été mis à jour.');
        $I->see('L’adresse postale est obligatoire.');
        $I->see('Le code postal est obligatoire.');
        $I->see('La ville est obligatoire.');
    }

    /**
     * Try to send a profile with too long field.
     *
     * @before login
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendLongProfileForm(AcceptanceTester $I): void
    {
        $codePostal = str_repeat('s', 6);
        $telephone = str_repeat('s', 22);
        $label = str_repeat('s', 33);

        $I->wantTo('send a too long profile form.');
        $I->amOnPage('/customer/profile');
        $I->fillField('Prénom', $label);
        $I->fillField('Nom de famille', $label);
        $I->fillField('Téléphone', $telephone);
        $I->fillField('Numéro de TVA', $label);
        //Société
        $I->fillField('app_profile[society]', $label);
        $I->fillField('Adresse', $label);
        $I->fillField('Complément', $label);
        $I->fillField('Code postal', $codePostal);
        $I->fillField('Ville', $label);
        $I->click('Enregistrer les modifications');
        $I->seeCurrentUrlEquals('/customer/profile');
        $I->seeResponseCodeIsSuccessful();
        $I->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $I->dontSee('Votre profil a correctement été mis à jour.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 5 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 21 caractères.');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.');
    }

    /**
     * Try to send a society without its name.
     *
     * @before login
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendSocietyWithoutName(AcceptanceTester $I): void
    {
        $I->wantTo('send customer as a society without its name.');
        $I->amOnPage('/customer/profile');
        //Société
        $I->selectOption('app_profile[type]', 0);
        $I->click('Enregistrer les modifications');
        $I->seeCurrentUrlEquals('/customer/profile');
        $I->seeResponseCodeIsSuccessful();
        $I->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $I->dontSee('Votre profil a correctement été mis à jour.');
        $I->see('Pour les professionnels, le nom de la société est obligatoire.');
    }

    /**
     * Try to send a society without its name.
     *
     * @before login
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToSendPhysicWithoutName(AcceptanceTester $I): void
    {
        $I->wantTo('send customer as a physic person without its name.');
        $I->amOnPage('/customer/profile');
        //Physic person
        $I->selectOption('app_profile[type]', 1);
        $I->fillField('Nom de famille', '');
        $I->click('Enregistrer les modifications');
        $I->seeCurrentUrlEquals('/customer/profile');
        $I->seeResponseCodeIsSuccessful();
        $I->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $I->dontSee('Votre profil a correctement été mis à jour.');
        $I->see('Pour les particuliers, le nom de famille est obligatoire.');
    }
}
