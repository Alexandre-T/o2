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

use App\Entity\Bill;
use App\Entity\Order;
use App\Entity\Programmation;
use App\Entity\User;

interface MailerInterface
{
    /**
     * Send a mail to accountant from sender to inform about the new order and the new bill.
     *
     * @param Order  $order      the new order
     * @param Bill   $bill       the new bill
     * @param string $sender     the sender of mail
     * @param string $accountant the accountant who received mail
     *
     * @return int
     */
    public function sendPaymentMail(Order $order, Bill $bill, string $sender, string $accountant): int;

    /**
     * Send an email to programmer to inform that a new programmation was ordered.
     *
     * @param Programmation $programmation the new programmation
     * @param string        $programmer    the mail programmer
     * @param string        $sender        sender of mail
     *
     * @return int
     */
    public function sendProgrammationMail(Programmation $programmation, string $programmer, string $sender): int;

    /**
     * Send an email to reset password.
     *
     * @param User $user Mail recipient
     */
    public function sendResettingEmailMessage(User $user): void;

    /**
     * Sent a mail to alert customer that his programmation is done.
     *
     * @param Programmation $programmation programmation done
     * @param string        $sender        expediter
     *
     * @return int
     */
    public function sendReturningProgrammation(Programmation $programmation, string $sender): int;

    /**
     * Send an internal test email to declared user in settings.
     *
     * @param string $email mail of senders and receivers
     *
     * @return int
     */
    public function sendTestMail(string $email): int;
}
