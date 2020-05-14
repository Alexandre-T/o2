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
use App\Entity\Payment;
use App\Entity\User;
use App\Factory\BillFactory;
use App\Model\OrderInterface;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

/**
 * Order fixtures.
 */
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var Article
     */
    private $fiveHundred;

    /**
     * @var Article
     */
    private $hundred;

    /**
     * @var Article
     */
    private $ten;

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
     *
     * @throws Exception returned by DateTimeImmutable
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array($_ENV['APP_ENV'], ['dev', 'test'], true)) {
            /** @var User $customer */
            $customer = $this->getReference('user_customer');
            $this->ten = $this->getReference('article_10');
            $this->hundred = $this->getReference('article_100');
            $this->fiveHundred = $this->getReference('article_500');

            //Customer had only clicked on order-credit.
            $carted = $this->createCreditOrder($customer, 0); //ID1
            $manager->persist($carted);

            //Customer had clicked on order-credit and select some items. //ID2
            $customer = $this->getReference('user_customer-1');
            $carted = $this->createCreditOrder($customer, 1, 2, 3);
            $manager->persist($carted);

            //Customer had clicked on order-credit and select monetico.//ID3
            $customer = $this->getReference('user_customer-2');
            $carted = $this->createCreditOrder($customer, 2, 0, 0);
            $carted->setStatusOrder(OrderInterface::STATUS_PENDING);
            $payment = $this->createPayment($carted, 42, 'monetico');
            $manager->persist($carted);
            $manager->persist($payment);

            //Customer had clicked on order-credit and select paypal_express and canceled payment. //ID4
            $customer = $this->getReference('user_customer-7');
            $carted = $this->createCreditOrder($customer, 3, 0, 0);
            $payment = $this->createPayment($carted, 666);
            $manager->persist($carted);
            $manager->persist($payment);

            //Customer had clicked on order-credit and select paypal_express and validate payment but paypal said:cancel
            $customer = $this->getReference('user_customer-7');
            $carted = $this->createCreditOrder($customer, 2, 0, 0);
            $payment = $this->createPayment($carted, 1005);
            $carted->setStatusOrder(OrderInterface::STATUS_CANCELED); //ID 5
            $manager->persist($carted);
            $manager->persist($payment);

            //Customer had clicked on order-credit and select paypal_express and validate payment but paypal said: paid.
            $customer = $this->getReference('user_customer-7');
            $carted = $this->createCreditOrder($customer, 2, 0, 0);
            $payment = $this->createPayment($carted, 1006);
            $carted->setStatusOrder(OrderInterface::STATUS_PAID); //ID 6
            $manager->persist($carted);
            $manager->persist($payment);

            //Customer had clicked on order-credit and select paypal_express and validate payment but paypal said: paid.
            foreach (range(1, 2) as $index) {
                $customer = $this->getReference('user_customer-7');
                $carted = $this->createCreditOrder($customer, 1 + $index, 0, 0);
                $payment = $this->createPayment($carted, 1006 + $index);
                $carted->setStatusOrder(OrderInterface::STATUS_PENDING); //ID 7 and 8
                $manager->persist($carted);
                $manager->persist($payment);
            }

            //Customer had clicked on order-credit and select paypal_express and paid.
            $customer = $this->getReference('user_customer-4');
            foreach (range(1, 30) as $index) { // ID 9 to 38
                $quantity = ($index % 8) + 1;
                $carted = $this->createCreditOrder($customer, $quantity, 0, 0);
                $payment = $this->createPayment($carted, $index);

                //Create bill with confirmation.
                $bill = BillFactory::create($carted, $customer);
                $carted->setStatusOrder(OrderInterface::STATUS_PAID);
                $bill->setPaidAt(new DateTimeImmutable());
                $manager->persist($bill);
                $manager->persist($carted);
                $manager->persist($payment);
                //DO NOT REMOVE IT
                $manager->flush();
            }

            //Create 6 Cmd order
            foreach (range(1, 6) as $index) { //ID 39-44
                $customer = $this->getReference('user_customer-'. $index);
                $order = $this->createCmdOrder($customer);
                $manager->persist($order);
            }

            //Create 3 olsx orders for each olsx customer //ID 45-68
            foreach (range(1, UserFixtures::OLSX + 1) as $index) {
                $customer = $this->getReference('user_olsx-'. ($index));
                foreach(range(0, 4)as $status) {
                    $order = $this->createOlsxOrder($customer, $index, $status);
                    $manager->persist($order);
                }
            }

            //Create an order to be credited by accountant in Cest for olsx-7
            $order = $this->createCreditOrder($customer, 1, 0, 0);
            $order->setStatusOrder(OrderInterface::STATUS_PAID);
            $manager->persist($order);

            $manager->flush();
        }

        $manager->flush();
    }

    /**
     * Create a credit order.
     *
     * @param User $customer    Associated customer
     * @param int  $ten         Number of 10
     * @param int  $hundred     Number of 100
     * @param int  $fiveHundred Number of 500
     */
    private function createCreditOrder(
        User $customer,
        int $ten,
        int $hundred = 0,
        int $fiveHundred = 0
    ): Order {
        $vatRate = (float) $customer->getVat();
        $order = new Order();
        $order->setCustomer($customer);
        $order->setStatusOrder(OrderInterface::STATUS_CARTED);
        $order->addOrderedArticle($this->createOrdered($this->ten, $ten, $vatRate));
        $order->addOrderedArticle($this->createOrdered($this->hundred, $hundred, $vatRate));
        $order->addOrderedArticle($this->createOrdered($this->fiveHundred, $fiveHundred, $vatRate));
        $order->setCredits($ten * 10 + $hundred * 100 + $fiveHundred * 500);
        $order->setNature(OrderInterface::NATURE_CREDIT);
        $order->refreshPrice();
        $order->refreshVat();

        return $order;
    }

    /**
     * Create a CMD order.
     *
     * @param User $customer Associated customer
     */
    private function createCmdOrder(
        User $customer
    ): Order {
        $vatRate = (float) $customer->getVat();
        $order = new Order();
        $order->setCustomer($customer);
        $order->setStatusOrder($customer->getId() % 3);
        /* @var Article $cmd This is a CMD Slave */
        $cmd = $this->getReference('cmd_slave');
        $order->addOrderedArticle($this->createOrdered($cmd, 1, $vatRate));
        $order->setCredits(0);
        $order->setNature(OrderInterface::NATURE_CMD);
        $order->refreshPrice();
        $order->refreshVat();

        return $order;
    }

    /**
     * Create a OLSX order.
     *
     * @param User $customer Associated customer
     * @param int  $ten      Number of 10
     * @param int  $status   Status of Order
     */
    private function createOlsxOrder(
        User $customer,
        int $ten,
        int $status
    ): Order {
        $vatRate = (float) $customer->getVat();
        $order = new Order();
        $order->setCustomer($customer);
        $order->setStatusOrder($status);
        /* @var Article $olsx this is a ten package of olsx credit */
        $olsx = $this->getReference('olsx_10');
        $order->addOrderedArticle($this->createOrdered($olsx, $ten, $vatRate));
        $order->setCredits($ten * 10);
        $order->setNature(OrderInterface::NATURE_OLSX);
        $order->refreshPrice();
        $order->refreshVat();

        return $order;
    }

    /**
     * Create an ordered article.
     *
     * @param Article $article  Associated article
     * @param int     $quantity Quantity ordered
     * @param float   $vatRate  Customer vat rate
     */
    private function createOrdered(Article $article, int $quantity, float $vatRate): OrderedArticle
    {
        $ordered = new OrderedArticle();
        $ordered->setArticle($article);
        $ordered->setQuantity($quantity);
        $ordered->setPrice($article->getPrice());
        $ordered->setVat($article->getPrice() * $vatRate);

        return $ordered;
    }

    /**
     * Create a payment where number is index.
     *
     * @param Order  $carted  the order of payment
     * @param int    $index   the number of the payment to retrieve during acceptance
     * @param string $gateway the gateway code
     */
    private function createPayment(Order $carted, int $index, string $gateway = 'paypal-express-checkout'): Payment
    {
        $customer = $carted->getCustomer();
        $payment = new Payment();
        $payment->setOrder($carted);
        $payment->setTotalAmount($carted->getAmount());
        $payment->setClientEmail($customer->getMail());
        $payment->setClientId($customer->getId());
        $payment->setDescription($gateway);
        $payment->setCurrencyCode('EUR');
        $payment->setNumber($index);

        return $payment;
    }
}
