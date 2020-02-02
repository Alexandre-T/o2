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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

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
        $order->setStatusCredit(OrderInterface::CREDITED_ALREADY);
        $order->setStatusOrder(OrderInterface::STATUS_PAID);

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
        $order->setStatusCredit(OrderInterface::CREDITED_ALREADY);
        $customer->setCredit($customer->getCredit() + $order->getCredits());

        $this->entityManager->persist($customer);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
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
     */
    public function getNonEmptyCartedOrder($user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $orders = $repository->findByUserNonEmptyStatusCreditOrder($user, OrderInterface::STATUS_CARTED);

        if (null === $orders || empty($orders)) {
            throw new NoOrderException('No carted order for this user');
        }

        return $orders[0];
    }

    /**
     * Find the only one non-paid OLSX order or create a new one.
     *
     * @param User $user the user criteria
     */
    public function getOrCreateCartedOlsxOrder(User $user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $order = $repository->findOneByUserAndCartedOlsxCreditOrder($user);

        if (null === $order) {
            $order = new Order();
            $order->setNature(OrderInterface::NATURE_OLSX);
            $order->setCustomer($user);
            $order->setStatusOrder(OrderInterface::STATUS_CARTED);
        }

        return $order;
    }

    /**
     * Find the only one non-paid order or create a new one.
     *
     * @param User $user the user criteria
     */
    public function getOrCreateCartedStandardOrder(User $user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $order = $repository->findOneByUserAndCartedStandardCreditOrder($user);

        if (null === $order) {
            $order = new Order();
            $order->setNature(OrderInterface::NATURE_CREDIT);
            $order->setCustomer($user);
            $order->setStatusOrder(OrderInterface::STATUS_CARTED);
        }

        return $order;
    }

    /**
     * Return pending orders for given customer.
     *
     * @param User $customer Given customer
     *
     * @return Order[]
     */
    public function getPending(User $customer): array
    {
        return $this->getMainRepository()->findByUserAndStatusOrder($customer, OrderInterface::STATUS_PENDING);
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
     * Retrieve all OLSX articles, create some OrderedArticle and add them to order. Then calculate prices.
     *
     * @param Order       $order the order
     * @param CreditOrder $model the credit order model
     */
    public function pushOlsxOrderedArticles(Order $order, CreditOrder $model): void
    {
        $this->pushOrderedArticles($order, $model, OrderInterface::NATURE_OLSX);
    }

    /**
     * Retrieve all standard articles, create some OrderedArticle and add them to order. Then calculate prices.
     *
     * @param Order       $order the order
     * @param CreditOrder $model the credit order model
     */
    public function pushStandardOrderedArticles(Order $order, CreditOrder $model): void
    {
        $this->pushOrderedArticles($order, $model, OrderInterface::NATURE_CREDIT);
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
     */
    public function retrieveOrCreateCmdOrder(User $user): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $orders = $repository->findCmdByUserAndStatusOrder($user, OrderInterface::STATUS_CARTED);

        if (null === $orders || empty($orders)) {
            return $this->createdCmdArticle($user);
        }

        $order = $orders[0];
        $this->verifyVat($order);

        return $order;
    }

    /**
     * Set order to cancel status.
     *
     * @param Order $order order to update
     */
    public function setCancel(Order $order): void
    {
        $order->setStatusOrder(OrderInterface::STATUS_CANCELED);
    }

    /**
     * Set order as paid and credits user.
     *
     * @param Order $order order to put at paid
     */
    public function setPaid(Order $order): void
    {
        $order->setStatusOrder(OrderInterface::STATUS_PAID);

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
        $order->setStatusOrder(OrderInterface::STATUS_PENDING);
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
        $order->setStatusOrder(OrderInterface::STATUS_CARTED);
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
     * Handle articles of a specified nature, create some OrderedArticle, add them to the order, calculates price.
     *
     * @param Order       $order  order to complete
     * @param CreditOrder $model  model to provide data
     * @param int         $nature Nature is a constant of OrderInterface
     */
    private function pushOrderedArticles(Order $order, CreditOrder $model, int $nature): void
    {
        $articleRepository = $this->entityManager->getRepository(Article::class);
        switch ($nature) {
            case OrderInterface::NATURE_CREDIT:
                /* @var Article[] $articles the articles returns by repository */
                $articles = $articleRepository->findStandardCredit();

                break;
            case OrderInterface::NATURE_OLSX:
                /* @var Article[] $articles the articles returns by repository */
                $articles = $articleRepository->findOlsxCredit();

                break;
            default:
                $articles = [];
        }

        $order->setCredits(0);
        $order->setPrice(0);
        $order->setVat(0);
        $vatRate = (float) $order->getCustomer()->getVat();
        //TODO SPRINT3: replaced this hardcode by something more dependent from model
        $methods[10] = 'getTen';
        $methods[50] = 'getFifty';
        $methods[100] = 'getHundred';
        $methods[500] = 'getFiveHundred';

        foreach ($articles as $article) {
            if (array_key_exists($article->getCredit(), $methods)) {
                $this->updateOrder($order, $article, $model->{$methods[$article->getCredit()]}(), $vatRate);
            }
        }
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
