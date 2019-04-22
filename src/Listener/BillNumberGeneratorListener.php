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

use App\Entity\Bill;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Bill Number Generator Listener.
 */
class BillNumberGeneratorListener implements EventSubscriber
{
    /**
     * This subscriber will listen prePersist and preUpdate event.
     *
     * @return array of events this subscriber wants to listen to
     */
    public function getSubscribedEvents()
    {
        return ['prePersist'];
    }

    /**
     * This function is called before persist.
     *
     * @param LifecycleEventArgs $args provided by lifecycle
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Bill) {
            //This is not a User, so we quit.
            return;
        }

        /** @var Bill $entity */
        if (null !== $entity->getNumber()) {
            return;
        }

        $billRepository = $args->getEntityManager()->getRepository(Bill::class);
        $max = max(0, $billRepository->maxNumber() + 1);
        $entity->setNumber($max);
    }
}
