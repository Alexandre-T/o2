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

namespace App\DataFixtures;

use App\Entity\StatusOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Status order fixtures.
 */
class StatusOrderFixtures extends Fixture
{
    /**
     * Load status order.
     *
     * @param ObjectManager $manager manager to save data
     */
    public function load(ObjectManager $manager): void
    {
        //Cancel status
        $canceled = new StatusOrder();
        $canceled->setCode(StatusOrder::CANCELED);
        $canceled->setCanceled(true);

        //In the cart and not paid
        $carted = new StatusOrder();
        $carted->setCode(StatusOrder::CARTED);

        //Paid status
        $paid = new StatusOrder();
        $paid->setCode(StatusOrder::PAID);
        $paid->setPaid(true);

        //Pending status
        $pending = new StatusOrder();
        $pending->setCode(StatusOrder::PENDING);
        $pending->setPending(true);


        //These references are used.
        $this->addReference('status_order_canceled', $canceled);
        $this->addReference('status_order_carted', $carted);
        $this->addReference('status_order_paid', $paid);
        $this->addReference('status_order_pending', $pending);

        //Persist prod data
        $manager->persist($canceled);
        $manager->persist($carted);
        $manager->persist($paid);
        $manager->persist($pending);

        $manager->flush();
    }
}
