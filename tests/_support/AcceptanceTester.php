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

use Codeception\Actor;
use Codeception\Lib\Friend;

/**
 * Inherited Methods.
 *
 * @method void   wantToTest($text)
 * @method void   wantTo($text)
 * @method void   execute($callable)
 * @method void   expectTo($prediction)
 * @method void   expect($prediction)
 * @method void   amGoingTo($argumentation)
 * @method void   am($role)
 * @method void   lookForwardTo($achieveValue)
 * @method void   comment($description)
 * @method Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Opens the page for the given relative URI.
     *
     * ``` php
     * <?php
     * // opens front page
     * $you->areOnPage('/');
     * // opens /register page
     * $you->areOnPage('/register');
     * ```
     *
     * @param string $page given relative URL
     *
     * @return mixed|null
     *
     * @see \Codeception\Lib\InnerBrowser::amOnPage()
     */
    public function areOnPage(string $page)
    {
        return $this->amOnPage($page);
    }

    /**
     * I login with user and password provided.
     *
     * @param string      $user     $user without (at)example.org
     * @param string|null $password password will equals user if not defined
     */
    public function login(string $user, string $password = null): void
    {
        $mail = $user.'@example.org';
        $password = $password ?? $user;
        $this->comment("I want to login with {$mail} and {$password}");
        $this->amOnPage('/login');

        $this->seeResponseCodeIsSuccessful();
        $this->seeCurrentUrlEquals('/login');
        $this->fillField('Adresse email', $mail);
        $this->fillField('Mot de passe', $password);
        $this->click('Se connecter');
        $this->seeResponseCodeIsSuccessful();
        $this->seeCurrentUrlEquals('/');
        $this->seeLink('Déconnexion');
    }
}
