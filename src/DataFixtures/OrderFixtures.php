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

use App\Entity\Article;
use App\Entity\Order;
use App\Entity\OrderedArticle;
use App\Entity\User;
use App\Factory\BillFactory;
use App\Model\OrderInterface;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\Payment\CoreBundle\Entity\PaymentInstruction;

/**
 * Order fixtures.
 */
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * Load orders.
     *
     * @param ObjectManager $manager manager to save data
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array(getenv('APP_ENV'), ['dev', 'test'])) {
            /** @var User $customer */
            $customer = $this->getReference('user_customer');
            /** @var Article $ten */
            /** @var Article $hundred */
            /** @var Article $fiveHundred */
            $ten = $this->getReference('article_10');
            $hundred = $this->getReference('article_100');
            $fiveHundred = $this->getReference('article_500');

            //Customer had only clicked on order-credit.
            $carted = new Order();
            $carted->setCustomer($customer);
            $carted->setStatusOrder(OrderInterface::CARTED);
            $carted->setCredits(0);
            $carted->setPrice(0);
            $carted->setVat(0);
            $manager->persist($carted);

            //Customer had clicked on order-credit and select some items.
            $customer = $this->getReference('user_customer-1');
            $carted = new Order();
            $carted->setCustomer($customer);
            $carted->setStatusOrder(OrderInterface::CARTED);
            $carted->addOrderedArticle($this->createOrdered($ten, 1));
            $carted->addOrderedArticle($this->createOrdered($hundred, 2));
            $carted->addOrderedArticle($this->createOrdered($fiveHundred, 3));
            $carted->setCredits(1710);
            $carted->setPrice(15600);
            $carted->setVat(3120);
            $manager->persist($carted);

            //Customer had clicked on order-credit and select paypal_express.
            $customer = $this->getReference('user_customer-2');
            $carted = new Order();
            $carted->setCustomer($customer);
            $carted->setStatusOrder(OrderInterface::CARTED);
            $carted->addOrderedArticle($this->createOrdered($ten, 2));
            $carted->addOrderedArticle($this->createOrdered($hundred, 0));
            $carted->addOrderedArticle($this->createOrdered($fiveHundred, 0));
            $instruction = new PaymentInstruction(240, 'EUR', 'paypal_express_checkout');
            $carted->setPaymentInstruction($instruction);
            $carted->setStatusOrder(OrderInterface::PENDING);
            $carted->setCredits(20);
            $carted->setPrice(200);
            $carted->setVat(40);

            $manager->persist($carted);
            $manager->persist($instruction);

            //Customer had clicked on order-credit and select paypal_express and canceled payment.
            $customer = $this->getReference('user_customer-3');
            $carted = new Order();
            $carted->setCustomer($customer);
            $carted->setStatusOrder(OrderInterface::CARTED);
            $carted->addOrderedArticle($this->createOrdered($ten, 3));
            $carted->addOrderedArticle($this->createOrdered($hundred, 0));
            $carted->addOrderedArticle($this->createOrdered($fiveHundred, 0));
            $carted->setCredits(30);
            $carted->setPrice(300);
            $carted->setVat(60);
            //create payment instruction
            $instruction = new PaymentInstruction(300, 'EUR', 'paypal_express_checkout');
            $carted->setPaymentInstruction($instruction);
            $carted->setStatusOrder(OrderInterface::PENDING);
            //TODO create payment
            //Canceled (Nothing to do ?)
            //TODO On controller::PaymentCanceled Do something to trace it.
            $manager->persist($carted);
            $manager->persist($instruction);

            //Customer had clicked on order-credit and select paypal_express and paid.
            $customer = $this->getReference('user_customer-4');
            foreach (range(1,30) as $index) {
                $quantity = $index % 8 + 1;
                $carted = new Order();
                $carted->setCustomer($customer);
                $carted->setStatusOrder(OrderInterface::PAID);
                $carted->addOrderedArticle($this->createOrdered($ten, $quantity));
                $carted->addOrderedArticle($this->createOrdered($hundred, 0));
                $carted->addOrderedArticle($this->createOrdered($fiveHundred, 0));
                $instruction = new PaymentInstruction($quantity * 100, 'EUR', 'paypal_express_checkout');
                $carted->setPaymentInstruction($instruction);
                $carted->setCredits($quantity * 10);
                $carted->setPrice($quantity * 100);
                $carted->setVat($quantity * 20);
                //TODO create payment,
                //TODO Payment
                //Create bill with confirmation.
                $bill = BillFactory::create($carted, $customer);
                $carted->setStatusOrder(OrderInterface::PAID);
                $bill->setPaidAt(new DateTimeImmutable());
                $manager->persist($bill);
                $manager->persist($carted);
                $manager->persist($instruction);
                $manager->flush();
            }
        }

        $manager->flush();
    }

    /**
     * Create an ordered article.
     *
     * @param Article $article  Associated article
     * @param int     $quantity Quantity ordered
     *
     * @return OrderedArticle
     */
    private function createOrdered(Article $article, int $quantity): OrderedArticle
    {
        $ordered = new OrderedArticle();
        $ordered->setArticle($article);
        $ordered->setQuantity($quantity);
        $ordered->setUnitCost($article->getPrice());

        return $ordered;
    }
}
