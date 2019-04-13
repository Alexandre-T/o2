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
    public function getNonPaidOrder(User $user): Order
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
                if ($model->getTen()) {
                    $orderedArticle = $this->createdOrderedArticle($order, $article, $model->getTen());
                    $order->addOrderedArticle($orderedArticle);
                    $order->setCredits($model->getTen() * $article->getCredit() + $order->getCredits());
                    $order->setPrice($model->getTen() * (double) $article->getCost() + $order->getPrice());
                }

                continue;
            }

            if (100 === $article->getCredit()) {
                if ($model->getHundred()) {
                    $orderedArticle = $this->createdOrderedArticle($order, $article, $model->getHundred());
                    $order->addOrderedArticle($orderedArticle);
                    $order->setCredits($model->getHundred() * $article->getCredit() + $order->getCredits());
                    $order->setPrice($model->getHundred() * (double) $article->getCost() + $order->getPrice());
                }

                continue;
            }

            if (500 === $article->getCredit()) {
                if ($model->getFiveHundred()) {
                    $orderedArticle = $this->createdOrderedArticle($order, $article, $model->getFiveHundred());
                    $order->addOrderedArticle($orderedArticle);
                    $order->setCredits($model->getFiveHundred() * $article->getCredit() + $order->getCredits());
                    $order->setPrice($model->getFiveHundred() * (double) $article->getCost() + $order->getPrice());
                }

                continue;
            }
        }
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

        return $orderedArticle;
    }
}
