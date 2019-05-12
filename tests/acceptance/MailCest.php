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
 * Administration mail Cest.
 */
class MailCest
{
    /**
     * You try to send a test mail.
     *
     * @param AcceptanceTester $you acceptance tester
     */
    public function tryToSendTestMail(AcceptanceTester $you): void
    {
        $you->wantTo('send a test mail');
        $you->login('all');
        $you->amOnPage('/');
        $you->click('Mails', 'nav');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Formulaire de test pour envoyer un mail');
        $you->click('Envoyer le mail de test');
        $you->seeResponseCodeIsSuccessful();
        $you->see('Aucun problème n’a été détecté lors de l’envoi du mail');
    }
}
