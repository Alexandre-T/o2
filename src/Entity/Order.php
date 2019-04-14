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

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Order entity.
 *
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(
 *     name="te_order",
 *     schema="data",
 *     options={"comment": "order data table"},
 *     indexes={
 *         @ORM\Index(name="ndx_user",  columns={"customer_id"}),
 *         @ORM\Index(name="ndx_status_order",  columns={"status_order_id"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_order_number",  columns={"number"})
 *     }
 * )
 *
 * @Gedmo\Loggable
 */
class Order implements ConstantInterface, EntityInterface, PostalAddressInterface
{
    /*
     * Person trait.
     */
    use PersonTrait;

    /*
     * Postal address trait
     */
    use PostalAddressTrait;

    /**
     * Credits gained by this order.
     *c.
     *
     * @ORM\Column(type="smallint")
     */
    private $credits;

    /**
     * Customer, owner of order.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="usr_id")
     *
     * @Gedmo\Versioned
     */
    private $customer;

    /**
     * Identifier.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", name="id")
     */
    private $identifier;

    /**
     * Number of order.
     *
     * Value have to be unique.
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Gedmo\Versioned
     */
    private $number;

    /**
     * Price.
     *
     * TODO find type.
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Gedmo\Versioned
     */
    private $price;

    /**
     * Payment timestamp.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Versioned
     */
    private $paymentAt;

    /**
     * Is user credit sold increased?
     *
     * @ORM\Column(type="boolean", options={"default": false})
     *
     * @Gedmo\Versioned
     */
    private $statusCredit = false;

    /**
     * Status order.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\StatusOrder")
     * @ORM\JoinColumn(nullable=false, name="status_order_id")
     *
     * @Gedmo\Versioned
     */
    private $statusOrder;

    /**
     * VAT price in euro.
     *
     * TODO Find type.
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Gedmo\Versioned
     */
    private $vat;

    /**
     * Ordered articles.
     *
     * @var Collection|OrderedArticle[]
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\OrderedArticle",
     *     mappedBy="order",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    private $orderedArticles;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->orderedArticles = new ArrayCollection();
    }

    /**
     * Credit getter.
     *
     * @return int|null
     */
    public function getCredits(): ?int
    {
        return $this->credits;
    }

    /**
     * Identifier getter.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->identifier;
    }

    /**
     * Return the label of entity.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return sprintf('%06d', $this->number);
    }

    /**
     * Order number getter.
     *
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * Payment timestamp.
     *
     * @return DateTimeInterface|null
     */
    public function getPaymentAt(): ?DateTimeInterface
    {
        return $this->paymentAt;
    }

    /**
     * Price getter.
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Status credit getter.
     *
     * @return bool|null
     */
    public function getStatusCredit(): ?bool
    {
        return $this->statusCredit;
    }

    /**
     * Status order getter.
     *
     * @return StatusOrder|null
     */
    public function getStatusOrder(): ?StatusOrder
    {
        return $this->statusOrder;
    }

    /**
     * VAT in euro.
     *
     * TODO find type
     *
     * @return mixed
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Status credit getter.
     *
     * @return bool|null
     */
    public function isCredited(): ?bool
    {
        return $this->statusCredit;
    }

    /**
     * Was this order paid.
     *
     * @return bool
     */
    public function isPaid(): ?bool
    {
        if (null === $this->getStatusOrder()) {
            return null;
        }

        return $this->getStatusOrder()->isPaid() && !$this->getStatusOrder()->isCanceled();
    }

    /**
     * Credits fluent setter.
     *
     * @param int $credits credit bought
     *
     * @return Order
     */
    public function setCredits(int $credits): self
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * Set payment timestamp.
     *
     * @param DateTimeInterface|null $paymentAt Payment timestamp
     *
     * @return Order
     */
    public function setPaymentAt(?DateTimeInterface $paymentAt): self
    {
        $this->paymentAt = $paymentAt;

        return $this;
    }

    /**
     * Price fluent setter.
     *
     * TODO find type of price
     *
     * @param mixed $price price without VAT
     *
     * @return Order
     */
    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Status credit fluent setter.
     *
     * @param bool $statusCredit the new credit status
     *
     * @return Order
     */
    public function setStatusCredit(bool $statusCredit): self
    {
        $this->statusCredit = $statusCredit;

        return $this;
    }

    /**
     * Status order fluent setter.
     *
     * @param StatusOrder $statusOrder the new status order
     *
     * @return Order
     */
    public function setStatusOrder(StatusOrder $statusOrder): self
    {
        $this->statusOrder = $statusOrder;

        return $this;
    }

    /**
     * VAT fluent setter.
     *
     * TODO find the instance
     *
     * @param mixed $vat new vat price
     *
     * @return Order
     */
    public function setVat($vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Order number fluent setter.
     *
     * @param int $number new order number
     *
     * @return Order
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Customer getter.
     *
     * @return User|null
     */
    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    /**
     * Customer fluent setter.
     *
     * @param User|null $customer new customer
     *
     * @return Order
     */
    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Ordered articles getter.
     *
     * @return Collection|OrderedArticle[]
     */
    public function getOrderedArticles(): Collection
    {
        return $this->orderedArticles;
    }

    /**
     * Add an ordered article to collection.
     *
     * @param OrderedArticle $orderedArticle to add
     *
     * @return Order
     */
    public function addOrderedArticle(OrderedArticle $orderedArticle): self
    {
        if (!$this->orderedArticles->contains($orderedArticle)) {
            $this->orderedArticles[] = $orderedArticle;
            $orderedArticle->setOrder($this);
        }

        return $this;
    }

    /**
     * Remove an ordered article from collection.
     *
     * @param OrderedArticle $orderedArticle to remove
     *
     * @return Order
     */
    public function removeOrderedArticle(OrderedArticle $orderedArticle): self
    {
        if ($this->orderedArticles->contains($orderedArticle)) {
            $this->orderedArticles->removeElement($orderedArticle);
            // set the owning side to null (unless already changed)
            if ($orderedArticle->getOrder() === $this) {
                $orderedArticle->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * Get ordered article by article if exists.
     *
     * @param Article $article article filter
     *
     * @return OrderedArticle|null
     */
    public function getOrderedByArticle(Article $article): ?OrderedArticle
    {
        foreach ($this->getOrderedArticles() as $orderedArticle) {
            if (null === $orderedArticle->getArticle()) {
                return null;
            }

            if ($orderedArticle->getArticle() === $article) {
                return $orderedArticle;
            }
        }

        return null;
    }
}
