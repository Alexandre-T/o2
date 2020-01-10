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

use App\Model\OrderInterface;
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
 *     options={
 *         "comment": "order data table", "engine": "InnoDb", "charset": "utf8mb4",
 *         "collate": "utf8mb4_unicode_ci"
 *     },
 *     indexes={
 *         @ORM\Index(name="ndx_user",  columns={"customer_id"}),
 *         @ORM\Index(name="ndx_user_status",  columns={"customer_id", "status_order"}),
 *         @ORM\Index(name="ndx_status_order",  columns={"status_order"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_order_uuid",  columns={"uuid"})
 *     }
 * )
 *
 * @Gedmo\Loggable
 */
class Order implements EntityInterface, OrderInterface, PriceInterface
{
    use PriceTrait;

    /**
     * Bills for this order.
     *
     * @var Collection|Bill[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Bill", mappedBy="order")
     */
    private $bills;

    /**
     * Credits gained by this order.
     *
     * @var int
     *
     * @ORM\Column(type="smallint")
     *
     * @Gedmo\Versioned
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
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", name="id")
     */
    private $identifier;

    /**
     * Nature of order.
     *
     * @ORM\Column(type="smallint")
     *
     * @Gedmo\Versioned
     */
    private $nature;

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
     * @ORM\OrderBy({"price": "ASC"})
     */
    private $orderedArticles;

    /**
     * Payer Id is information about payer returned by Paypal.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     *
     * @Gedmo\Versioned
     */
    private $payerId;

    /**
     * All payments for the current order.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="order")
     *
     * @var Payment[]|ArrayCollection
     */
    private $payments;

    /**
     * Price without taxes.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Gedmo\Versioned
     */
    private $price;

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
     * @ORM\Column(type="smallint", options={"default": OrderInterface::CARTED})
     *
     * @Gedmo\Versioned
     */
    private $statusOrder = OrderInterface::CARTED;

    /**
     * Token is information provided by paypal.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     *
     * @Gedmo\Versioned
     */
    private $token;

    /**
     * Order uuid.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=23)
     *
     * @Gedmo\Versioned
     */
    private $uuid;

    /**
     * VAT price in euro.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Gedmo\Versioned
     */
    private $vat;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->orderedArticles = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->generateUuid();
        $this->payments = new ArrayCollection();
    }

    /**
     * Bill fluent adder.
     *
     * @param Bill $bill bill to add
     *
     * @return Order
     */
    public function addBill(Bill $bill): self
    {
        if (! $this->bills->contains($bill)) {
            $this->bills[] = $bill;
            $bill->setOrder($this);
        }

        return $this;
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
        if (! $this->orderedArticles->contains($orderedArticle)) {
            $this->orderedArticles[] = $orderedArticle;
            $orderedArticle->setOrder($this);
        }

        $this->refreshPrice();
        $this->refreshVat();

        return $this;
    }

    /**
     * Add a payment.
     *
     * @param Payment $payment the payment
     *
     * @return Order
     */
    public function addPayment(Payment $payment): self
    {
        if (! $this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setOrder($this);
        }

        return $this;
    }

    /**
     * Bills getter.
     *
     * @return Collection|Bill[]
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    /**
     * Credit getter.
     */
    public function getCredits(): ?int
    {
        return $this->credits;
    }

    /**
     * Customer getter.
     */
    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    /**
     * Identifier getter.
     */
    public function getId(): ?int
    {
        return $this->identifier;
    }

    /**
     * Return the label of entity.
     */
    public function getLabel(): string
    {
        return sprintf('%06d', $this->identifier);
    }

    /**
     * Nature getter.
     */
    public function getNature(): ?int
    {
        return $this->nature;
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
     * Get ordered article by article if exists.
     *
     * @param Article $article article filter
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

    /**
     * Payer id getter.
     */
    public function getPayerId(): ?string
    {
        return $this->payerId;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    /**
     * Status order getter.
     */
    public function getStatusOrder(): ?int
    {
        return $this->statusOrder;
    }

    /**
     * Token getter.
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Order uuid getter.
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Is this order canceled.
     */
    public function isCanceled(): bool
    {
        return OrderInterface::CANCELED === $this->statusOrder;
    }

    /**
     * Is this order carted.
     */
    public function isCarted(): bool
    {
        return OrderInterface::CARTED === $this->statusOrder;
    }

    /**
     * Status credit getter.
     */
    public function isCredited(): ?bool
    {
        return $this->statusCredit;
    }

    /**
     * Was this order paid.
     */
    public function isPaid(): bool
    {
        return OrderInterface::PAID === $this->statusOrder;
    }

    /**
     * Is this order pending.
     */
    public function isPending(): bool
    {
        return OrderInterface::PENDING === $this->statusOrder;
    }

    /**
     * Refresh Price.
     *
     * @return Order
     */
    public function refreshPrice(): self
    {
        $this->setPrice(0.0);
        foreach ($this->getOrderedArticles() as $orderedArticle) {
            $this->setPrice(
                (float) $this->getPrice() + $orderedArticle->getQuantity() * (float) $orderedArticle->getPrice()
            );
        }

        return $this;
    }

    /**
     * Refresh uuid.
     *
     * @return Order
     */
    public function refreshUuid(): self
    {
        $this->generateUuid();

        return $this;
    }

    /**
     * Refresh vat.
     *
     * @return Order
     */
    public function refreshVat(): self
    {
        $this->setVat(0.0);
        foreach ($this->getOrderedArticles() as $orderedArticle) {
            $this->setVat(
                $this->getVat() + $orderedArticle->getQuantity() * (float) $orderedArticle->getVat()
            );
        }

        return $this;
    }

    /**
     * Bill fluent remover.
     *
     * @param Bill $bill bill to remove
     *
     * @return Order
     */
    public function removeBill(Bill $bill): self
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            // set the owning side to null (unless already changed)
            if ($bill->getOrder() === $this) {
                $bill->setOrder(null);
            }
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

        $this->refreshPrice();
        $this->refreshVat();

        return $this;
    }

    /**
     * Remove a payment from list.
     *
     * @param Payment $payment Payment
     *
     * @return Order
     */
    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getOrder() === $this) {
                $payment->setOrder(null);
            }
        }

        return $this;
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
     * Nature fluent setter.
     *
     * @param int $nature the nature
     *
     * @return $this
     */
    public function setNature(int $nature): self
    {
        $this->nature = $nature;

        return $this;
    }

    /**
     * Payer id fluent setter.
     *
     * @param string $payerId payer id is a data provided by paypal
     *
     * @return Order
     */
    public function setPayerId(string $payerId): self
    {
        $this->payerId = $payerId;

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
     * @param int $statusOrder the new status order
     *
     * @return Order
     */
    public function setStatusOrder(int $statusOrder): self
    {
        $this->statusOrder = $statusOrder;

        return $this;
    }

    /**
     * Token fluent setter.
     *
     * @param string|null $token token is a data provided by paypal
     *
     * @return Order
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Generate a new uuid for $uuid property.
     */
    private function generateUuid(): void
    {
        $this->uuid = uniqid('', true);
    }
}
