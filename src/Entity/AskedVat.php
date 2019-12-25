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

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This entity stores all vat asked by customer.
 * It stores the decision taken by accountant too.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AskedVatRepository")
 * @ORM\Table(
 *     name="te_askedvat",
 *     options={"comment": "Store customers asking new VAT profile and accountant decisions"},
 *     indexes={
 *         @ORM\Index(name="ndx_askedvat_customer", columns={"customer_id"}),
 *         @ORM\Index(name="ndx_askedvat_accountant", columns={"accountant_id"})
 *     }
 * )
 *
 * @Gedmo\Loggable
 */
class AskedVat implements EntityInterface
{
    public const ACCEPTED = 1;
    public const REJECTED = 2;
    public const STATUS = [self::ACCEPTED, self::REJECTED, self::UNDECIDED];
    public const UNDECIDED = 0;

    /**
     * The accountant deciding the validity of question.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="usr_id", name="accountant_id")
     */
    private $accountant;

    /**
     * The code could be the VAT INTRA NUMBER of user.
     *
     * @ORM\Column(type="string", length=63, nullable=true)
     *
     * @Assert\Length(max="65")
     *
     * @Gedmo\Versioned
     */
    private $code;

    /**
     * Date of question.
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * The customer asking a new VAT.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="usr_id", name="customer_id")
     */
    private $customer;

    /**
     * Identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * The decision of accountant.
     *
     * @ORM\Column(type="smallint")
     *
     * @Assert\Choice(choices=AskedVat::STATUS, message="error.asked-vat-status.choices")
     *
     * @Gedmo\Versioned
     */
    private $status = self::UNDECIDED;

    /**
     * The VAT asked by customer.
     *
     * @ORM\Column(type="decimal", precision=4, scale=4)
     *
     * @Gedmo\Versioned
     */
    private $vat;

    /**
     * AskedVat constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * The accountant getter.
     */
    public function getAccountant(): ?User
    {
        return $this->accountant;
    }

    /**
     * The code getter.
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Entity creation getter.
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * The customer getter.
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
        return $this->id;
    }

    /**
     * Return the label of entity.
     */
    public function getLabel(): string
    {
        if (null === $this->getCustomer()) {
            return '';
        }

        return $this->getCustomer()->getLabel();
    }

    /**
     * The status of decision getter.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * The asked VAT getter.
     */
    public function getVat(): ?string
    {
        return $this->vat;
    }

    /**
     * The accountant fluent setter.
     *
     * @param User|null $accountant the accountant deciding
     *
     * @return $this
     */
    public function setAccountant(?User $accountant): self
    {
        $this->accountant = $accountant;

        return $this;
    }

    /**
     * The code fluent setter.
     *
     * @param string|null $code the intra VAT number or null
     *
     * @return $this
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * The customer fluent setter.
     *
     * @param User $customer the customer asking a new VAT
     *
     * @return $this
     */
    public function setCustomer(User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * The status of decision setter.
     *
     * @param int $status the new status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * The VAT fluent setter.
     *
     * @param string $vat the new asked VAT
     *
     * @return $this
     */
    public function setVat(string $vat): self
    {
        $this->vat = $vat;

        return $this;
    }
}
