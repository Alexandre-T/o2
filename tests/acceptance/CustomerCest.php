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
    public function tryToBuyNotOwnedOrder(AcceptanceTester $you): void
    {
        $you->wantTo('payment page of a not owned order');
        $you->login('customer-8');
        $you->areOnPage('/payment/method-choose/2');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/customer/order-credit');
        $you->see('Votre commande n’a pas été retrouvée. Veuillez la ressaisir');
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
        $you->areOnPage('/customer/password');
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
     * Try to list orders.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToListOrders(AcceptanceTester $you): void
    {
        $you->wantTo('List canceled orders of customer');
        //Customer 7 had a lot a of orders.
        $you->login('customer-7');
        $you->click('Commandes annulées');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/customer/orders/canceled');
        $you->click('Commandes en attente');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/customer/orders/pending');
        $you->click('Commandes payées');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/customer/orders/paid');
    }

    /**
     * Try to order cmd slave.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToOrderCmdWithMonetico(AcceptanceTester $you): void
    {
        $you->wantTo('connect as customer and try to order a cmd slave with cb');
        $you->login('customer');
        $you->areOnPage('/customer/order-cmd');
        $you->seeResponseCodeIsSuccessful();
        $identifier = $you->grabFromCurrentUrl('~/payment/method-choose/(\d+)~');
        $you->seeCurrentUrlEquals('/payment/method-choose/'.$identifier);
        $you->selectOption('choose_payment_method[method]', 'monetico');
        $you->click('Poursuivre');
        $you->seeResponseCodeIsSuccessful();
        $token = $you->grabFromCurrentUrl('~/payment/capture/([\w|-]+)~');
        $you->seeCurrentUrlEquals('/payment/capture/'.$token);
    }

    /**
     * Try to order cmd slave with paypal.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToOrderCmdWithPaypal(AcceptanceTester $you): void
    {
        $you->wantTo('connect as customer and try to order a cmd slave with paypal');
        $you->login('customer');
        $you->areOnPage('/customer/order-cmd');
        $you->selectOption('choose_payment_method[method]', 'paypal_express_checkout');
        $you->click('Poursuivre');
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
        $you->areOnPage('/customer/order-credit');
        $you->fillField('Lot(s) de 10 crédits', 4);
        $you->fillField('Lot(s) de 50 crédits', 3);
        $you->fillField('Lot(s) de 100 crédits', 1);
        $you->fillField('Lot(s) de 500 crédits', 2);
        $you->click('Enregistrer votre commande');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/payment/method-choose/1');
        $you->see('Poursuivre');
    }

    /**
     * Try to order olsx.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToOrderOlsxWithMonetico(AcceptanceTester $you): void
    {
        $you->wantTo('connect as customer and try to order a cmd slave with cb');
        $you->login('olsx-1');
        $you->click('Acheter des crédits OLSX');
        $you->seeCurrentUrlEquals('/customer/order-olsx');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Crédits OLSX supplémentaires');
        $you->fillField('Lot(s) de 10 crédits', 1);
        $you->fillField('Lot(s) de 50 crédits', 2);
        $you->fillField('Lot(s) de 100 crédits', 3);
        $you->fillField('Lot(s) de 500 crédits', 4);
        $you->click('Enregistrer votre commande');
        $you->seeResponseCodeIsSuccessful();
        $identifier = $you->grabFromCurrentUrl('~/payment/method-choose/(\d+)~');
        $you->seeCurrentUrlEquals('/payment/method-choose/'.$identifier);
        $you->selectOption('choose_payment_method[method]', 'monetico');
        $you->click('Poursuivre');
        $you->seeResponseCodeIsSuccessful();
        $token = $you->grabFromCurrentUrl('~/payment/capture/([\w|-]+)~');
        $you->seeCurrentUrlEquals('/payment/capture/'.$token);
    }

    /**
     * You try to buy credits without Order.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToPurchaseProgrammation(AcceptanceTester $you): void
    {
        $you->wantTo('Purchase a programmation');
        $you->login('customer');
        $you->areOnPage('/customer/programmation/new');
        $you->seeResponseCodeIsSuccessful();
        $you->click('Commander', 'button');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/customer/programmation/new');
        $you->dontSee('error.'); //All error messages are translated
        $you->fillField('Marque', 'Marque');
        $you->fillField('Modèle', 'Modèle');
        $you->fillField('Version', 'Version');
        $you->fillField('N° de série', 'Numéro');
        $you->fillField('Année', '2100');
        $you->fillField('Kilométrage', '32009');
        $you->fillField('Cylindrée', '2.32');
        $you->fillField('Puissance', '42');
        $you->fillField('Outil de lecture', 'outil');
        $you->selectOption('programmation_form[read]', '1');
        $you->selectOption('programmation_form[gearAutomatic]', '0');
        $you->selectOption('programmation_form[odb]', '1');
        $you->attachFile('programmation_form[originalFile][file]', 'upload.txt');
        $you->click('Commander', 'button');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/customer/programmation/new');
        $you->dontSee('error.'); //All error messages are translated
        $you->fillField('Année', '2014');
        $you->fillField('programmation_form[catOff]', true); //HIDDEN
        $you->fillField('programmation_form[edcOff]', true); //HIDDEN
        $you->fillField('programmation_form[egrOff]', true); //HIDDEN
        $you->fillField('programmation_form[ethanol]', true); //HIDDEN
        $you->fillField('programmation_form[fapOff]', true); //HIDDEN
        $you->fillField('programmation_form[dtcOff]', true); //HIDDEN
        $you->fillField('programmation_form[truckFile]', true); //HIDDEN
        $you->fillField('programmation_form[gear]', true); //HIDDEN
        $you->attachFile('programmation_form[originalFile][file]', 'upload.txt');
        $you->click('Commander', 'button');
        $you->seeResponseCodeIsSuccessful();
        $programmationId = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/customer/programmation/'.$programmationId);
        $you->seeNumberOfElements('span.badge.badge-success', 8);
        $you->seeNumberOfElements('span.badge.badge-secondary', 10);
        $you->click('Mes fichiers');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/customer/programmation/list');
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
        $you->areOnPage('/customer/password');
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
        $you->areOnPage('/customer/profile');
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
        $you->areOnPage('/customer/password');
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
        $you->areOnPage('/customer/profile');
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
        $you->areOnPage('/customer/profile');
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
        $you->areOnPage('/customer/profile');
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
     * Try to send a new empty vat profile.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToSendVat(AcceptanceTester $you): void
    {
        $you->wantTo('send an empty vat profile.');
        $you->login('customer');
        $you->areOnPage('/customer/vat');
        $you->see('Votre taux de TVA actuel est de');
        $you->click('Envoyer la demande');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Le nouveau taux de TVA est identique au taux actuel. Demande rejetée');
        $you->fillField('Explications', '');
        $you->selectOption('app_vat[vat]', '0.0850');
        $you->click('Envoyer la demande');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Veuillez confirmer votre code postal.');
        $you->selectOption('app_vat[vat]', '0.0000');
        $you->fillField('Explications', '');
        $you->click('Envoyer la demande');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Veuillez confirmer votre numéro de TVA intra-communautaire.');

        $you->wantTo('send a valid vat profile.');
        $you->fillField('Explications', '97100');
        $you->selectOption('app_vat[vat]', '0.0850');
        $you->click('Envoyer la demande');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/');
        $you->see('La demande de changement de taux de TVA a bien été envoyée à notre service comptable.');
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
        $you->areOnPage('/customer/profile');
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
        $you->areOnPage('/customer/password');
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
