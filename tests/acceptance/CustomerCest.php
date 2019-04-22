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
     * You try to buy credits without Order.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToBuyCreditsWithoutOrder(AcceptanceTester $you): void
    {
        $you->wantTo('buy credits without order');
        $you->login('customer-7');
        $you->amOnPage('/customer/buy-credit');
        $you->seeResponseCodeIsSuccessful();
        $you->canSeeCurrentUrlEquals('/customer/select-credit');
    }

    /**
     * Try to hack password with a wrong password.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToHackPassword(AcceptanceTester $you): void
    {
        $you->wantTo('update my password.');
        $you->login('customer');
        $you->amOnPage('/customer/password');
        $you->fillField('Ancien mot de passe', 'bidon');
        $you->fillField('Nouveau mot de passe', 'customer');
        $you->fillField('Confirmation', 'customer');
        $you->click('Enregistrer le nouveau mot de passe');
        $you->seeCurrentUrlEquals('/customer/password');
        $you->seeResponseCodeIsSuccessful();
        $you->see("Attention\u{a0}! Votre mot de passe n’a pas été mis à jour.");
        $you->dontSee('Votre mot de passe a correctement été mis à jour.');
        $you->see('Cette valeur ne correspond pas à votre ancien mot de passe.');
    }

    /**
     * Try to order some credit.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToOrderCredit(AcceptanceTester $you): void
    {
        $you->wantTo('connect as customer and try to order some credits');
        $you->login('customer');
        $you->amOnPage('/customer/select-credit');
        $you->fillField('Lot(s) de 10 crédits', 4);
        $you->fillField('Lot(s) de 100 crédits', 1);
        $you->fillField('Lot(s) de 500 crédits', 2);
        $you->click('Enregistrer votre commande');
        $you->seeResponseCodeIsSuccessful();
        $you->canSeeCurrentUrlEquals('/customer/buy-credit');
    }

    /**
     * Try to send an empty profile form.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSendAnEmptyPassword(AcceptanceTester $you): void
    {
        $you->wantTo('send an empty password form.');
        $you->login('customer');
        $you->amOnPage('/customer/password');
        $you->fillField('Ancien mot de passe', '');
        $you->fillField('Nouveau mot de passe', '');
        $you->fillField('Confirmation', '');
        $you->click('Enregistrer le nouveau mot de passe');
        $you->seeCurrentUrlEquals('/customer/password');
        $you->seeResponseCodeIsSuccessful();
        $you->see("Attention\u{a0}! Votre mot de passe n’a pas été mis à jour.");
        $you->dontSee('Votre mot de passe a correctement été mis à jour.');
        $you->see('Cette valeur ne correspond pas à votre ancien mot de passe.');
        $you->see('L’ancien mot de passe est obligatoire.');
    }

    /**
     * Try to send an empty profile form.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSendAnEmptyProfile(AcceptanceTester $you): void
    {
        $you->wantTo('send an empty profile form.');
        $you->login('customer');
        $you->amOnPage('/customer/profile');
        $you->fillField('Nom de famille', '');
        $you->fillField('Adresse', '');
        $you->fillField('Code postal', '');
        $you->fillField('Ville', '');
        $you->click('Enregistrer les modifications');
        $you->seeCurrentUrlEquals('/customer/profile');
        $you->seeResponseCodeIsSuccessful();
        $you->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $you->dontSee('Votre profil a correctement été mis à jour.');
        $you->see('L’adresse postale est obligatoire.');
        $you->see('Le code postal est obligatoire.');
        $you->see('La ville est obligatoire.');
    }

    /**
     * Try to send a password form with too long field.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSendLongPasswordForm(AcceptanceTester $you): void
    {
        $password = str_repeat('s', 4097);

        $you->wantTo('send a too long profile form.');
        $you->login('customer');
        $you->amOnPage('/customer/password');
        $you->fillField('Ancien mot de passe', $password);
        $you->fillField('Nouveau mot de passe', $password);
        $you->fillField('Confirmation', $password);
        $you->click('Enregistrer le nouveau mot de passe');
        $you->seeCurrentUrlEquals('/customer/password');
        $you->seeResponseCodeIsSuccessful();
        $you->see("Attention\u{a0}! Votre mot de passe n’a pas été mis à jour.");
        $you->dontSee('Votre mot de passe a correctement été mis à jour.');
        $you->see('Cette chaîne est trop longue. Elle doit avoir au maximum 4096 caractères.');
    }

    /**
     * Try to send a profile with too long field.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSendLongProfileForm(AcceptanceTester $you): void
    {
        $codePostal = str_repeat('s', 6);
        $telephone = str_repeat('s', 22);
        $label = str_repeat('s', 33);

        $you->wantTo('send a too long profile form.');
        $you->login('customer');
        $you->amOnPage('/customer/profile');
        $you->fillField('Prénom', $label);
        $you->fillField('Nom de famille', $label);
        $you->fillField('Téléphone', $telephone);
        $you->fillField('Numéro de TVA', $label);
        //Société
        $you->fillField('app_profile[society]', $label);
        $you->fillField('Adresse', $label);
        $you->fillField('Complément', $label);
        $you->fillField('Code postal', $codePostal);
        $you->fillField('Ville', $label);
        $you->click('Enregistrer les modifications');
        $you->seeCurrentUrlEquals('/customer/profile');
        $you->seeResponseCodeIsSuccessful();
        $you->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $you->dontSee('Votre profil a correctement été mis à jour.');
        $you->see('Cette chaîne est trop longue. Elle doit avoir au maximum 5 caractères.');
        $you->see('Cette chaîne est trop longue. Elle doit avoir au maximum 21 caractères.');
        $you->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.');
    }

    /**
     * Try to send a society without its name.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSendPhysicWithoutName(AcceptanceTester $you): void
    {
        $you->wantTo('send customer as a physic person without its name.');
        $you->login('customer');
        $you->amOnPage('/customer/profile');
        //Physic person
        $you->selectOption('app_profile[type]', 1);
        $you->fillField('Nom de famille', '');
        $you->click('Enregistrer les modifications');
        $you->seeCurrentUrlEquals('/customer/profile');
        $you->seeResponseCodeIsSuccessful();
        $you->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $you->dontSee('Votre profil a correctement été mis à jour.');
        $you->see('Pour les particuliers, le nom de famille est obligatoire.');
    }

    /**
     * Try to send a society without its name.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSendSocietyWithoutName(AcceptanceTester $you): void
    {
        $you->wantTo('send customer as a society without its name.');
        $you->login('customer');
        $you->amOnPage('/customer/profile');
        //Société
        $you->selectOption('app_profile[type]', 0);
        $you->click('Enregistrer les modifications');
        $you->seeCurrentUrlEquals('/customer/profile');
        $you->seeResponseCodeIsSuccessful();
        $you->see("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $you->dontSee('Votre profil a correctement été mis à jour.');
        $you->see('Pour les professionnels, le nom de la société est obligatoire.');
    }

    /**
     * Try to update customer.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToUpdateCustomer(AcceptanceTester $you): void
    {
        $you->wantTo('update customer.');
        $you->login('customer');
        $you->amOnPage('/customer/profile');
        $you->selectOption('app_profile[type]', 1);
        $you->fillField('Nom de famille', 'Nom de famille');
        $you->fillField('Adresse', 'rue de Bordeaux');
        $you->fillField('Code postal', '33160');
        $you->fillField('Ville', 'Mérignac');
        $you->selectOption('Pays', 'FR');
        $you->click('Enregistrer les modifications');
        $you->seeCurrentUrlEquals('/customer/profile');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSee("Attention\u{a0}! Votre profil n’a pas été mis à jour.");
        $you->see('Votre profil a correctement été mis à jour.');
    }

    /**
     * Try to update password.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToUpdatePassword(AcceptanceTester $you): void
    {
        $you->wantTo('update my password.');
        $you->login('customer');
        $you->amOnPage('/customer/password');
        $you->fillField('Ancien mot de passe', 'customer');
        $you->fillField('Nouveau mot de passe', 'customer');
        $you->fillField('Confirmation', 'customer');
        $you->click('Enregistrer le nouveau mot de passe');
        $you->seeCurrentUrlEquals('/');
        $you->seeResponseCodeIsSuccessful();
        $you->dontSee("Attention\u{a0}! Votre mot de passe n’a pas été mis à jour.");
        $you->see('Votre mot de passe a correctement été mis à jour.');
    }
}
