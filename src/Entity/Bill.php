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
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BillRepository")
 * @ORM\Table(
 *     name="te_bill",
 *     schema="data",
 *     options={"comment": "bill data table"},
 *     indexes={
 *         @ORM\Index(name="ndx_bill_customer",  columns={"customer_id"}),
 *         @ORM\Index(name="ndx_bill_order",  columns={"order_id"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_bill_number", columns={"number"})
 *     }
 * )
 *
 * @Gedmo\Loggable
 */
class Bill implements EntityInterface, PersonInterface, PostalAddressInterface, PriceInterface
{
    /*
     * Traits declaration.
     */
    use PersonTrait;
    use PostalAddressTrait;
    use PriceTrait;

    /**
     * Cancel date time.
     *
     * Null if not canceled
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Versioned
     */
    private $canceledAt;

    /**
     * Date time creation.
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * Customer.
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\User", inversedBy="bills", fetch="LAZY")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="usr_id", name="customer_id")
     *
     * @Gedmo\Versioned
     */
    private $customer;

    /**
     * Serial identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", name="id")
     */
    private $identifier;

    /**
     * Bill number.
     *
     * @ORM\Column(type="integer")
     *
     * @Gedmo\Versioned
     */
    private $number;

    /**
     * Order.
     *
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="\App\Entity\Order", inversedBy="bills")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Gedmo\Versioned
     */
    private $order;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Versioned
     */
    private $paidAt;

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
     * Get amount already paid.
     *
     * @return DateTimeInterface|null
     */
    public function getAlreadyPaid(): ?float
    {
        if ($this->isPaid()) {
            return $this->getAmount();
        }

        return 0.0;
    }

    /**
     * Cancel date time getter.
     *
     * @return DateTimeInterface|null
     */
    public function getCanceledAt(): ?DateTimeInterface
    {
        return $this->canceledAt;
    }

    /**
     * Creation datetime getter.
     *
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
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
        return sprintf('WEB%06d', $this->number);
    }

    /**
     * Number bill getter.
     *
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
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
     * Datetime payment getter.
     *
     * @return DateTimeInterface|null
     */
    public function getPaidAt(): ?DateTimeInterface
    {
        return $this->paidAt;
    }

    /**
     * Get amount to paid.
     *
     * @return float|null
     */
    public function getToPaid(): ?float
    {
        if ($this->isPaid() || $this->isCanceled()) {
            return 0.0;
        }

        return $this->getAmount();
    }

    /**
     * Is this bill canceled?
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->getCanceledAt() instanceof DateTimeInterface;
    }

    /**
     * Is this bill paid?
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->getPaidAt() instanceof DateTimeInterface;
    }

    /**
     * Cancel datetime fluent setter.
     *
     * @param DateTimeInterface|null $canceledAt Cancel datetime
     *
     * @return Bill
     */
    public function setCanceledAt(?DateTimeInterface $canceledAt): self
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    /**
     * Customer fluent setter.
     *
     * @param User|null $customer customer
     *
     * @return Bill
     */
    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Number fluent setter.
     *
     * @param int|null $number Number
     *
     * @return Bill
     */
    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Order fluent setter.
     *
     * @param Order|null $order Order
     *
     * @return Bill
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Datetime paid setter.
     *
     * @param DateTimeInterface|null $paidAt datetime of payment
     *
     * @return Bill
     */
    public function setPaidAt(?DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }
}
