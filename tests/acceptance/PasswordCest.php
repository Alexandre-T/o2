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
 * Password Cest.
 *
 * Test process to reset a password.
 */
class PasswordCest
{
    /**
     * Try to access init password page with a non-existent token.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToInitPasswordWithInvalidToken(AcceptanceTester $I): void
    {
        $I->wantTo('init password with non-existent token.');
        $I->amOnPage('/password-reset?token=toto');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/password-lost');
        $I->see('Le mail pour changer de mot de passe est périmé.');
    }

    /**
     * Try to access init password page without token.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToInitPasswordWithoutToken(AcceptanceTester $I): void
    {
        $I->wantTo('init password without token.');
        $I->amOnPage('/password-reset');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/password-lost');
        $I->see('Le mail pour changer de mot de passe est périmé.');
    }

    /**
     * Try to access init password page without token.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToInitPasswordWithValidToken(AcceptanceTester $I): void
    {
        $I->wantTo('init password without token.');
        $I->amOnPage('/password-reset?token=resetToken3');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/password-reset?token=resetToken3');
        $I->see('Réinitialisation du mot de passe');
        $I->click('Réinitialiser le mot de passe');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/password-reset?token=resetToken3');
        $I->see('Réinitialisation du mot de passe');
        $I->see('Le mot de passe est obligatoire');
        $I->fillField('Mot de passe', 'foobarfoo');
        $I->fillField('Confirmation', 'foobarfoo');
        $I->click('Réinitialiser le mot de passe');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/login');
        $I->see('Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.');
        //The token is no more existent
        $I->amOnPage('/password-reset?token=resetToken3');
        $I->seeResponseCodeIsSuccessful();
        $I->seeCurrentUrlEquals('/password-lost');
        $I->see('Le mail pour changer de mot de passe est périmé.');
        $I->login('customer-3', 'foobarfoo');
    }

    /**
     * Try to reset my lost password.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToResetPassword(AcceptanceTester $I): void
    {
        $I->wantTo('reset my lost password.');
        $I->amOnPage('/password-lost');
        $I->fillField('Adresse email', 'customer@example.org');
        $I->click('Demander un mail de récupération');
        $I->seeCurrentUrlEquals('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->see("Un mail pour changer votre mot de passe vient de vous être envoyé. Consultez votre messagerie\u{a0}!");
    }

    /**
     * Try to reset my lost password with a non-existent account.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToResetPasswordWithBadMail(AcceptanceTester $I): void
    {
        $I->wantTo('reset my lost password.');
        $I->amOnPage('/password-lost');
        $I->fillField('Adresse email', 'non-existent@example.org');
        $I->click('Demander un mail de récupération');
        $I->seeCurrentUrlEquals('/login');
        $I->seeResponseCodeIsSuccessful();
        $I->see("Un mail pour changer votre mot de passe vient de vous être envoyé. Consultez votre messagerie\u{a0}!");
    }

    /**
     * Try to reset my lost password with an empty mail.
     *
     * @param AcceptanceTester $I the acceptance tester
     */
    public function tryToResetPasswordWithEmptyMail(AcceptanceTester $I): void
    {
        $I->wantTo('reset my lost password.');
        $I->amOnPage('/password-lost');
        $I->fillField('Adresse email', '');
        $I->click('Demander un mail de récupération');
        $I->seeCurrentUrlEquals('/password-lost');
        $I->seeResponseCodeIsSuccessful();
        $I->see('L’adresse email est obligatoire');
    }
}
