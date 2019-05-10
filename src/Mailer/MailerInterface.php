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

namespace App\Mailer;

use App\Entity\User;

interface MailerInterface
{
    /**
     * Send an email to reset password.
     *
     * @param User $user Mail recipient
     */
    public function sendResettingEmailMessage(User $user): void;

    /**
     * Send an internal test email to declared user in settings.
     *
     * @param string $email mail of senders and receivers
     *
     * @return int
     */
    public function sendTestMail(string $email): int;
}
