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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderedArticleRepository")
 * @ORM\Table(
 *     name="tj_ordered_article",
 *     schema="data",
 *     options={"comment": "Ordered articles"},
 *     indexes={
 *         @ORM\Index(name="ndx_ordered_article_order",  columns={"order_id"}),
 *         @ORM\Index(name="ndx_ordered_article_full",  columns={"order_id", "article_id"}),
 *         @ORM\Index(name="ndx_ordered_article_article",  columns={"article_id"})
 *     }
 * )
 */
class OrderedArticle
{
    /**
     * Article.
     *
     * @var Article
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Article")
     * @ORM\JoinColumn(nullable=false, name="article_id", referencedColumnName="id")
     */
    private $article;

    /**
     * Order.
     *
     * @var Order
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="orderedArticles")
     * @ORM\JoinColumn(nullable=false, name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * Quantity ordered.
     *
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $quantity;

    /**
     * Unit cost.
     *
     * TODO find data type.
     *
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $unitCost;

    /**
     * Article getter.
     *
     * @return Article|null
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Item fluent setter.
     *
     * @param Article|null $article article ordered
     *
     * @return OrderedArticle
     */
    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Order getter.
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * Order fluent setter.
     *
     * @param Order|null $order linked order
     *
     * @return OrderedArticle
     */
    public function setOrder(?Order $order): self
    {
        if (null === $order) {
            $this->order = $order;

            return $this;
        }

        $order->addOrderedArticle($this);

        return $this;
    }

    /**
     * Quantity getter.
     *
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Quantity fluent setter.
     *
     * @param int $quantity quantity ordered
     *
     * @return OrderedArticle
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Unit cost getter.
     *
     * @return mixed
     */
    public function getUnitCost()
    {
        return $this->unitCost;
    }

    /**
     * Uniq cost fluent setter.
     *
     * @param mixed $unitCost unit cost
     *
     * @return OrderedArticle
     */
    public function setUnitCost($unitCost): self
    {
        $this->unitCost = $unitCost;

        return $this;
    }
}
