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

namespace App\Manager;

use App\Entity\Article;
use App\Entity\EntityInterface;
use App\Entity\Order;
use App\Entity\OrderedArticle;
use App\Entity\Payment;
use App\Entity\User;
use App\Exception\NoArticleException;
use App\Exception\NoOrderException;
use App\Form\Model\CreditOrder;
use App\Model\OrderInterface;
use App\Repository\ArticleRepository;
use App\Repository\OrderRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * order Manager.
 *
 * @property OrderRepository $repository
 */
class OrderManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Const for the alias query.
     */
    public const ALIAS = 'order';

    /**
     * Accountant validate an order.
     *
     * @param Order $order the order to validate
     */
    public function accountantValidate(Order $order): void
    {
        $order->setStatusCredit(OrderInterface::CREDITED);
        $order->setStatusOrder(OrderInterface::PAID);

        $user = $order->getCustomer();
        if (!$order->isCredited()) {
            $user->setCredit($user->getCredit() + $order->getCredits());
            $order->setStatusCredit(true);
        }
    }

    /**
     * Credit a customer.
     *
     * @param Order $order order to credit customer
     */
    public function credit(Order $order): void
    {
        $customer = $order->getCustomer();
        $order->setStatusCredit(OrderInterface::CREDITED);
        $customer->setCredit($customer->getCredit() + $order->getCredits());

        $this->entityManager->persist($customer);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /**
     * Get the only one carted order by user.
     *
     * @param User $user user filter
     *
     * @throws NoOrderException when array is empty
     *
     * @return Order
     */
    public function getCartedOrder(User $user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $orders = $repository->findByUserAndStatusCreditOrder($user, OrderInterface::CARTED);

        if (null === $orders || empty($orders)) {
            throw new NoOrderException('No carted order for this user');
        }

        return $orders[0];
    }

    /**
     * Return default alias.
     */
    public function getDefaultAlias(): string
    {
        return self::ALIAS;
    }

    /**
     * Get the default field for ordering data.
     *
     * @return string
     */
    public function getDefaultSortField(): string
    {
        return self::ALIAS.'.id';
    }

    /**
     * Get the only one non-empty carted order by user.
     *
     * @param User $user user filter
     *
     * @throws NoOrderException when array is empty
     *
     * @return Order
     */
    public function getNonEmptyCartedOrder($user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $orders = $repository->findByUserNonEmptyStatusCreditOrder($user, OrderInterface::CARTED);

        if (null === $orders || empty($orders)) {
            throw new NoOrderException('No carted order for this user');
        }

        return $orders[0];
    }

    /**
     * Find the only one non-paid order or create a new one.
     *
     * @param User $user the user criteria
     *
     * @return Order
     */
    public function getOrCreateCartedOrder(User $user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $order = $repository->findOneByUserAndCartedCreditOrder($user);

        if (null === $order) {
            $order = new Order();
            $order->setCustomer($user);
            $order->setStatusOrder(OrderInterface::CARTED);
        }

        return $order;
    }

    /**
     * Return the Query builder needed by the paginator.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->repository->createQueryBuilder(self::ALIAS);
    }

    /**
     * Is this entity deletable?
     *
     * @param EntityInterface|Order $entity the entity to test
     *
     * @return bool true if entity is deletable
     */
    public function isDeletable(EntityInterface $entity): bool
    {
        return !$entity->isPaid();
    }

    /**
     * Push data.
     *
     * @param Order       $order order to complete
     * @param CreditOrder $model model to provide data
     */
    public function pushOrderedArticles(Order $order, CreditOrder $model): void
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        /** @var Article[] $articles */
        //TODO SPRINT2: change the find all by another filter
        $articles = $articleRepository->findAll();
        $order->setCredits(0);
        $order->setPrice(0);
        $order->setVat(0);
        $vatRate = (float) $order->getCustomer()->getVat();
        $methods[10] = 'getTen';
        $methods[100] = 'getHundred';
        $methods[500] = 'getFiveHundred';

        foreach ($articles as $article) {
            if (array_key_exists($article->getCredit(), $methods)) {
                $this->updateOrder($order, $article, $model->{$methods[$article->getCredit()]}(), $vatRate);
            }
        }
    }

    /**
     * Retrieve an order find by its payment instruction.
     *
     * @param Payment $payment the payment
     *
     * @throws NoOrderException when order was not linked
     *
     * @return Order|null
     */
    public function retrieveByPayment(Payment $payment): Order
    {
        $order = $this->getMainRepository()->findOneByPayment($payment);
        if (null === $order) {
            throw new NoOrderException("Order with payment {$payment->getId()} is non-existent.");
        }

        return $order;
    }

    /**
     * Retrieve order by uuid.
     *
     * @param string $uuid uuid to retrieve order
     *
     * @throws NoOrderException when order does not exists
     *
     * @return Order
     */
    public function retrieveByUuid(string $uuid): Order
    {
        $order = $this->repository->findOneByUuid($uuid);
        if (null === $order) {
            throw new NoOrderException("Order with uuid {$uuid} is non-existent.");
        }

        return $order;
    }

    /**
     * Retrieve a CMD carted order or creates a CMD Slave order.
     *
     * @param User $user the user which wants to order a cmd slave
     *
     * @throws NoArticleException if cmdslave article does not exist in database
     *
     * @return Order
     */
    public function retrieveOrCreateCmdOrder(User $user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $orders = $repository->findCmdByUserAndStatusOrder($user, OrderInterface::CARTED);

        if (null === $orders || empty($orders)) {
            return $this->createdCmdArticle($user);
        }

        $order = $orders[0];
        $this->verifyVat($order);

        return $order;
    }

    /**
     * Set order as paid and credits user.
     *
     * @param Order $order order to put at paid
     */
    public function setPaid(Order $order): void
    {
        $order->setStatusOrder(OrderInterface::PAID);

        $user = $order->getCustomer();
        if (!$order->isCredited()) {
            $user->setCredit($user->getCredit() + $order->getCredits());
            $order->setStatusCredit(true);
        }
    }

    /**
     * Set order to pending status.
     *
     * @param Order $order order to update
     */
    public function setPending(Order $order): void
    {
        $order->setStatusOrder(OrderInterface::PENDING);
    }

    /**
     * Validate payment after payment complete.
     *
     * @param Order $order order to validate
     */
    public function validateAfterPaymentComplete(Order $order): void
    {
        $this->setPaid($order);
        $order->refreshUuid();
    }

    /**
     * Return the main repository.
     *
     * @return EntityRepository|OrderRepository|ObjectRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }

    /**
     * Create an order with a cml artcle.
     *
     * @param User $user the customer
     *
     * @throws NoArticleException when cmdslave does not exist
     *
     * @return Order
     */
    private function createdCmdArticle(User $user): Order
    {
        /** @var ArticleRepository $repository */
        $repository = $this->entityManager->getRepository(Article::class);
        $article = $repository->findOneByCode('cmdslave');

        if (!$article instanceof Article) {
            throw new NoArticleException('Article with code cmdslave does not exist.');
        }

        $orderedArticle = new OrderedArticle();
        $orderedArticle->setArticle($article);
        $orderedArticle->setQuantity(1);
        $orderedArticle->setPrice($article->getPrice());
        $orderedArticle->setVat($article->getPrice() * $user->getVat());

        $order = new Order();
        $order->setCustomer($user);
        $order->setCredits(0);
        $order->setNature(OrderInterface::NATURE_CMD);
        $order->setStatusOrder(OrderInterface::CARTED);
        $order->addOrderedArticle($orderedArticle);
        $order->refreshPrice();
        $order->refreshVat();

        return $order;
    }

    /**
     * Create an ordered article.
     *
     * @param Order   $order    linked order
     * @param Article $article  linked article
     * @param int     $quantity quantity wanted
     * @param float   $vat      the vate rate coming from customer
     *
     * @return OrderedArticle
     */
    private function createdOrderedArticle(Order $order, Article $article, int $quantity, float $vat): OrderedArticle
    {
        $orderedArticle = new OrderedArticle();
        $orderedArticle->setArticle($article);
        $orderedArticle->setOrder($order);
        $orderedArticle->setPrice($article->getPrice());
        $orderedArticle->setVat($article->getPrice() * $vat); //Override with customer VAT rate
        $orderedArticle->setQuantity($quantity);
        $order->addOrderedArticle($orderedArticle);

        return $orderedArticle;
    }

    /**
     * Update order by updating existing ordered article or by creating non-existent one.
     *
     * @param Order   $order    linked order
     * @param Article $article  linked article
     * @param int     $quantity quantity wanted
     * @param float   $vat      vat rate coming from user
     */
    private function updateOrder(Order $order, Article $article, int $quantity, float $vat): void
    {
        $quantity = max(0, $quantity);
        $orderedArticle = $order->getOrderedByArticle($article);
        $vatRate = (float) $order->getCustomer()->getVat();
        if (null === $orderedArticle) {
            $this->createdOrderedArticle($order, $article, $quantity, $vatRate);
        } elseif ($orderedArticle instanceof OrderedArticle) {
            $this->updateOrderedArticle($orderedArticle, $article, $quantity, $vatRate);
        }

        $order->setCredits($quantity * $article->getCredit() + $order->getCredits());
        $order->setPrice($quantity * (float) $article->getPrice() + $order->getPrice());
        $order->setVat($quantity * (float) $article->getPrice() * $vat + $order->getVat());
    }

    /**
     * Update quantity of ordered article, synchronize price.
     *
     * @param OrderedArticle $orderedArticle ordered article
     * @param Article        $article        article
     * @param int            $quantity       new quantity
     * @param float          $vateRate       vat rate
     */
    private function updateOrderedArticle(
     OrderedArticle $orderedArticle,
     Article $article,
     int $quantity,
     float $vateRate
    ): void {
        $orderedArticle->setPrice($article->getPrice());
        $orderedArticle->setVat($article->getPrice() * $vateRate);
        $orderedArticle->setQuantity($quantity);
    }

    /**
     * Verify VAT of an order.
     *
     * @param Order $order the order to verify
     */
    private function verifyVat(Order $order): void
    {
        $vateRate = $order->getCustomer()->getVat();
        $order->setVat(0);
        foreach ($order->getOrderedArticles() as $orderedArticle) {
            $orderedArticle->setVat($orderedArticle->getPrice() * $vateRate);
            $order->setVat($order->getVat() + $orderedArticle->getVat());
        }
    }
}
