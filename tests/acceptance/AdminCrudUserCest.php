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
 * Contact Cest.
 *
 * Test all actions available for organiser.
 */
class AdminCrudUserCest
{
    /**
     * Test the contact CRUD.
     *
     * @param AcceptanceTester $you the acceptance tester
     */
    public function tryToCrudUser(AcceptanceTester $you): void
    {
        $you->login('administrator');
        $you->click('Utilisateurs');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals('/administration/user');
        $you->see('Administrator');
        $you->see('administrator@example.org');
        $you->seeLink('Consulter');
        $you->seeLink('Éditer');
        $you->seeLink('2');
        $you->seeLink("Suivant\u{a0}»");

        $you->click('Créer');
        $you->fillField('Adresse email', 'codeception@example.org');
        $you->checkOption('#app_user_roles_1');
        $you->checkOption('#app_user_roles_2');
        $you->fillField('Mot de passe', 'foobar');
        $you->fillField('Confirmation', 'foobar');
        $you->fillField('Crédits', '42');
        $you->selectOption('app_user[type]', 1);
        $you->fillField('Prénom', 'Prénom');
        $you->fillField('Nom de famille', 'Nom de famille');
        $you->fillField('Société', 1);
        $you->fillField('Numéro de TVA', 'Numéro de TVA');
        $you->fillField('Adresse', 'rue de Bordeaux');
        $you->fillField('Complément', 'complément');
        $you->fillField('Code postal', '33160');
        $you->fillField('Ville', 'Mérignac');
        $you->selectOption('Pays', 'FR');
        $you->fillField('Téléphone', 'Téléphone');
        $you->click('Créer');
        $you->seeResponseCodeIsSuccessful();
        $identifier = $you->grabFromCurrentUrl('~(\d+)~');
        $you->seeCurrentUrlEquals('/administration/user/'.$identifier);
        $you->see('a été créé avec succès');
        $you->seeLink('Lister');
        $you->seeLink('Éditer');
        $you->see('Administrateur pouvant gérer les autres utilisateurs.');
        $you->see('Programmateur pouvant renvoyer les fichiers réponses aux clients.');
        $you->dontSee('Comptable pouvant gérer les crédits et les factures.');
        $you->see('Nouveau prénom');
        $you->see('Nouveau nom');
        $you->see('Nouveau mot de passe');
        $you->dontSee('entity.log.'); //All logs are translated!
        $you->click('Éditer l’utilisateur');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals("/administration/user/${identifier}/edit");
        $you->dontSee('Confirmation', 'label');
        $you->fillField('Nom de famille', 'AACodeception');
        $you->checkOption('#app_user_roles_0');
        $you->click('Éditer', '.btn-primary');
        $you->seeResponseCodeIsSuccessful();
        $you->see('a été modifié avec succès');
        $you->seeCurrentUrlEquals("/administration/user/${identifier}");
        $you->see('administrator@example.org');
        $you->see('Administrateur pouvant gérer les autres utilisateurs.');
        $you->click('Modifier le mot de passe de l’utilisateur');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals("/administration/user/${identifier}/password");
        $you->fillField('Mot de passe', 'foobarfoo');
        $you->fillField('Confirmation', 'foobar');
        $you->click('Mot de passe', '.btn-primary');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals("/administration/user/${identifier}/password");
        $you->see('Les mots de passe sont différents');
        $you->fillField('Mot de passe', 'foobarfoo');
        $you->fillField('Confirmation', 'foobarfoo');
        $you->click('Mot de passe', '.btn-primary');
        $you->see('a été modifié avec succès');
        $you->seeResponseCodeIsSuccessful();
        $you->seeCurrentUrlEquals("/administration/user/${identifier}");
        $you->submitForm('#delete_form_delete', []);
        $you->see('a été supprimé avec succès');
        $you->seeCurrentUrlEquals('/administration/user');
    }
}
