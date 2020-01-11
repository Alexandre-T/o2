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
 *     options={"comment": "Ordered articles"},
 *     indexes={
 *         @ORM\Index(name="ndx_ordered_article_order",  columns={"order_id"}),
 *         @ORM\Index(name="ndx_ordered_article_full",  columns={"order_id", "article_id"}),
 *         @ORM\Index(name="ndx_ordered_article_article",  columns={"article_id"})
 *     }
 * )
 */
class OrderedArticle implements PriceInterface
{
    use PriceTrait;

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
     * Price without taxes.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $price;

    /**
     * Quantity ordered.
     *
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $quantity;

    /**
     * VAT price in euro.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $vat;

    /**
     * Article getter.
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Order getter.
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * Quantity getter.
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
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
     * Order fluent setter.
     *
     * @param Order|null $order linked order
     *
     * @return OrderedArticle
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        if (null !== $order) {
            $order->addOrderedArticle($this);
        }

        return $this;
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
}
