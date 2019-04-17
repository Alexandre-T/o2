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
use App\Entity\StatusOrder;
use App\Entity\User;
use App\Exception\NoOrderException;
use App\Form\Model\CreditOrder;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * order Manager.
 */
class OrderManager extends AbstractRepositoryManager implements ManagerInterface
{
    /**
     * Const for the alias query.
     */
    public const ALIAS = 'order';

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
        return self::ALIAS.'.label';
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

        $order = $repository->findOneByUserAndCarted($user);

        if (null === $order) {
            // TODO create a factory
            $soRepository = $this->entityManager->getRepository(StatusOrder::class);
            $carted = $soRepository->findOneByCode(StatusOrder::CARTED);
            $order = new Order();
            $order->setCustomer($user);
            $order->setStatusOrder($carted);
        }

        return $order;
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
        $articles = $articleRepository->findAll();
        $order->setCredits(0);
        $order->setPrice(0);

        foreach ($articles as $article) {
            if (10 === $article->getCredit()) {
                $this->updateOrder($order, $article, $model->getTen());
                continue;
            }

            if (100 === $article->getCredit()) {
                $this->updateOrder($order, $article, $model->getHundred());
                continue;
            }

            if (500 === $article->getCredit()) {
                $this->updateOrder($order, $article, $model->getFiveHundred());
                continue;
            }
        }

        //TODO Create a vat for each article.
        $order->setVat($order->getPrice() * 0.2);
    }

    /**
     * Has this customer a carted order or not?
     *
     * @param User $user user filter
     *
     * @return bool
     */
    public function hasCartedOrder(User $user)
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        return 0 !== $repository->countByUserAndStatusOrder($user, StatusOrder::CARTED);
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

        $orders = $repository->findOneByUserAndStatusOrder($user, StatusOrder::CARTED);

        if (null === $orders || empty($orders)) {
            throw new NoOrderException('No carted order for this user');
        }

        return $orders[0];
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
    public function getNonEmptyCartedOrder($user)
    {
        /** @var OrderRepository $repository */
        $repository = $this->getMainRepository();

        $orders = $repository->findOneByUserNonEmptyStatusOrder($user, StatusOrder::CARTED);

        if (null === $orders || empty($orders)) {
            throw new NoOrderException('No carted order for this user');
        }

        return $orders[0];
    }

    /**
     * Set order as paid and credits user.
     *
     * @param Order $order order to put at paid
     */
    public function setOrderPaid(Order $order): void
    {
        $soRepository = $this->entityManager->getRepository(StatusOrder::class);
        $statusOrder = $soRepository->findOnePaid();
        $order->setStatusOrder($statusOrder);

        $user = $order->getCustomer();
        $user->setCredit($user->getCredit() + $order->getCredits());
        //FIXME create bill
    }

    /**
     * Return the main repository.
     *
     * @return EntityRepository
     */
    protected function getMainRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Order::class);
    }

    /**
     * Create an ordered article.
     *
     * @param Order   $order    linked order
     * @param Article $article  linked article
     * @param int     $quantity quantity wanted
     *
     * @return OrderedArticle
     */
    private function createdOrderedArticle(Order $order, Article $article, int $quantity): OrderedArticle
    {
        $orderedArticle = new OrderedArticle();
        $orderedArticle->setArticle($article);
        $orderedArticle->setOrder($order);
        $orderedArticle->setUnitCost($article->getCost());
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
     */
    private function updateOrder(Order $order, Article $article, int $quantity): void
    {
        $quantity = max(0, $quantity);
        $orderedArticle = $order->getOrderedByArticle($article);
        if (null === $orderedArticle) {
            $this->createdOrderedArticle($order, $article, $quantity);
        } elseif ($orderedArticle instanceof Article) {
            $this->updateOrderedArticle($orderedArticle, $article, $quantity);
        }

        $order->setCredits($quantity * $article->getCredit() + $order->getCredits());
        $order->setPrice($quantity * (float) $article->getCost() + $order->getPrice());
        $order->setVat($order->getPrice() * 0.2);
    }

    /**
     * Update quantity of ordered article, synchronize price.
     *
     * @param OrderedArticle $orderedArticle ordered article
     * @param Article        $article        article
     * @param int            $quantity       new quantity
     */
    private function updateOrderedArticle(OrderedArticle $orderedArticle, Article $article, int $quantity): void
    {
        $orderedArticle->setUnitCost($article->getCost());
        $orderedArticle->setQuantity($quantity);
    }
}
