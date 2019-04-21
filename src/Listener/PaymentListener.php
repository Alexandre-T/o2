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

namespace App\Listener;

use JMS\Payment\CoreBundle\PluginController\Event\Events;
use Symfony\Component\EventDispatcher\Event;

/**
 * Payment listener.
 */
class PaymentListener
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PAYMENT_INSTRUCTION_STATE_CHANGE => 'onPaymentInstructionStateChange',
            Events::PAYMENT_STATE_CHANGE => 'onPaymentStateChange',
        ];
    }

    /**
     * @param Event $event
     */
    public function onPaymentInstructionStateChange(Event $event): void
    {
        //FIXME log it?
    }

    /**
     * @param Event $event
     */
    public function onPaymentStateChange(Event $event): void
    {
        //FIXME log it?
        dump($event);
    }
}
