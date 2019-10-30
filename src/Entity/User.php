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
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Entity User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="ts_user",
 *     options={"comment": "Users data table"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_user_mail",  columns={"usr_mail"})
 *     }
 * )
 *
 * @Gedmo\Loggable
 *
 * @UniqueEntity(fields={"mail"},  message="error.mail.unique")
 */
class User implements EntityInterface, LanguageInterface, PersonInterface, PostalAddressInterface, Serializable, UserInterface
{
    /*
     * Trait declarations.
     */
    use LanguageTrait;
    use PersonTrait;
    use PostalAddressTrait;

    /**
     * Initial roles.
     */
    public const INITIAL_ROLES = [self::ROLE_USER];

    /**
     * Each available roles.
     */
    public const ROLE_ACCOUNTANT = 'ROLE_ACCOUNTANT';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_PROGRAMMER = 'ROLE_PROGRAMMER';
    public const ROLE_USER = 'ROLE_USER';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bill", mappedBy="customer")
     */
    private $bills;

    /**
     * Credits owned by user.
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="usr_credit", options={"unsigned": true, "comment": "User credits"})
     *
     * @Gedmo\Versioned
     */
    private $credit = 0;

    /**
     * Identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="usr_id", options={"unsigned": true, "comment": "User identifier"})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $identifier;

    /**
     * User mail and identifier.
     *
     * @var string
     *
     * @Assert\NotBlank(message="error.mail.blank")
     * @Assert\Length(max=255)
     * @Assert\Email
     *
     * @ORM\Column(type="string", unique=true, length=255, name="usr_mail", options={"comment": "User mail"})
     *
     * @Gedmo\Versioned
     */
    private $mail;

    /**
     * Customer orders.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="customer", orphanRemoval=true)
     */
    private $orders;

    /**
     * User encoded password.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=128, name="usr_password", options={"comment": "Encrypted password"})
     *
     * @Gedmo\Versioned
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @Assert\Length(min=6, max=4096)
     * @Assert\NotBlank(groups={"Registration"}, message="error.plain-password.blank")
     *
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Programmation", mappedBy="customer", orphanRemoval=true)
     */
    private $programmations;

    /**
     * Resetting password timestamp.
     *
     * @ORM\Column(type="datetime", nullable=true, options={"comment": "reset password timestamp"})
     *
     * @Gedmo\Versioned
     */
    private $resettingAt;

    /**
     * Resetting password token.
     *
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment": "token to reset password"})
     *
     * @Gedmo\Versioned
     */
    private $resettingToken;

    /**
     * Roles of this user.
     *
     * @var array
     *
     * @ORM\Column(type="json_array", nullable=false, name="usr_roles", options={"comment": "User roles"})
     *
     * @Gedmo\Versioned
     */
    private $roles = self::INITIAL_ROLES;

    /**
     * Terms of services.
     *
     * @Assert\IsTrue(groups={"Registration"}, message="error.tos.blank")
     *
     * @ORM\Column(type="boolean", name="usr_tos", options={"comment": "TOS accepted"})
     */
    private $tos = false;

    /**
     * VAT has no dependency and can be fix by admin.
     * But in fact, there is three profiles default (20%) DOM(8.50%) INTRA(0%)
     *
     * @var string|float
     *
     * @ORM\Column(type="decimal", precision=4, scale=2)
     */
    private $vat;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->programmations = new ArrayCollection();
    }

    /**
     * Bill fluent adder.
     *
     * @param Bill $bill bill to add
     *
     * @return User
     */
    public function addBill(Bill $bill): self
    {
        if (!$this->bills->contains($bill)) {
            $this->bills[] = $bill;
            $bill->setCustomer($this);
        }

        return $this;
    }

    /**
     * Add credit to current user.
     *
     * @param int $credit credit to add to user
     *
     * @return User
     */
    public function addCredit(int $credit): self
    {
        $this->credit += $credit;

        return $this;
    }

    /**
     * Order fluent adder.
     *
     * @param Order $order order to add
     *
     * @return User
     */
    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setCustomer($this);
        }

        return $this;
    }

    /**
     * Programmation fluent adder.
     *
     * @param Programmation $programmation programmation adder
     *
     * @return User
     */
    public function addProgrammation(Programmation $programmation): self
    {
        if (!$this->programmations->contains($programmation)) {
            $this->programmations[] = $programmation;
            $programmation->setCustomer($this);
        }

        return $this;
    }

    /**
     * Add a role.
     *
     * @param string $role role to add
     *
     * @return User
     */
    public function addRole(string $role): User
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Erase Credentials.
     *
     * @return User
     */
    public function eraseCredentials(): User
    {
        $this->plainPassword = null;

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
     *
     * @return int
     */
    public function getCredit(): int
    {
        return $this->credit;
    }

    /**
     * Id getter.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->identifier;
    }

    /**
     * Mail getter.
     *
     * @return string
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * The encoded password.
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Return the non-persistent plain password.
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Programmation getter.
     *
     * @return Collection|Programmation[]
     */
    public function getProgrammations(): Collection
    {
        return $this->programmations;
    }

    /**
     * Resetting timestamp getter.
     *
     * @return DateTimeInterface|null
     */
    public function getResettingAt(): ?DateTimeInterface
    {
        return $this->resettingAt;
    }

    /**
     * Resetting token getter.
     *
     * @return string|null
     */
    public function getResettingToken(): ?string
    {
        return $this->resettingToken;
    }

    /**
     * Return an array of all role codes to be compliant with UserInterface
     * This is NOT the Roles getter.
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * To implements UserInterface.
     *
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * A visual identifier that represents this user.
     * Username is the mail in this application.
     * TODO Change with getLabel()?
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->getMail();
    }

    /**
     * Has actual user the mentioned role?
     *
     * @param string $role role to test
     *
     * @return bool true if the user has the mentioned role
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Is this user a accountant.
     *
     * @return bool
     */
    public function isAccountant(): bool
    {
        return $this->hasRole(self::ROLE_ACCOUNTANT);
    }

    /**
     * Is this user an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Is this user a customer.
     *
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->hasRole(self::ROLE_USER);
    }

    /**
     * Is this user a programmer.
     *
     * @return bool
     */
    public function isProgrammer(): bool
    {
        return $this->hasRole(self::ROLE_PROGRAMMER);
    }

    /**
     * Terms of service getter.
     *
     * @return bool
     */
    public function isTos(): bool
    {
        return $this->tos;
    }

    /**
     * Bill fluent remover.
     *
     * @param Bill $bill bill to remove
     *
     * @return USer
     */
    public function removeBill(Bill $bill): self
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            // set the owning side to null (unless already changed)
            if ($bill->getCustomer() === $this) {
                $bill->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * Order fluent remover.
     *
     * @param Order $order order to remove
     *
     * @return User
     */
    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * Programmation fluent remover.
     *
     * @param Programmation $programmation programmation to remove
     *
     * @return User
     */
    public function removeProgrammation(Programmation $programmation): self
    {
        if ($this->programmations->contains($programmation)) {
            $this->programmations->removeElement($programmation);
            // set the owning side to null (unless already changed)
            if ($programmation->getCustomer() === $this) {
                $programmation->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * String representation of object.
     *
     * @see http://php.net/manual/en/serializable.serialize.php
     * @see \Serializable::serialize()
     *
     * @return string the string representation of the object or null
     */
    public function serialize(): string
    {
        return serialize(
            [
                $this->identifier,
                $this->credit,
                $this->givenName,
                $this->mail,
                $this->name,
                $this->password,
                $this->resettingAt,
                $this->resettingToken,
                $this->roles,
                $this->society,
                $this->type,
                $this->vat,
            ]
        );
    }

    /**
     * Credit fluent setter.
     *
     * @param int $credit new credit value
     *
     * @return User
     */
    public function setCredit(int $credit): User
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Setter of the mail.
     *
     * @param string $mail new mail
     *
     * @return User
     */
    public function setMail(?string $mail): User
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Setter of the password.
     *
     * @param string $password new password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the non-persistent plain password.
     *
     * @param string $plainPassword non-encrypted password
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        // @see https://knpuniversity.com/screencast/symfony-security/user-plain-password
        $this->password = null;

        return $this;
    }

    /**
     * Resetting timestamp fluent setter.
     *
     * @param DateTimeInterface|null $resettingAt timestamp reset
     *
     * @return User
     */
    public function setResettingAt(?DateTimeInterface $resettingAt): self
    {
        $this->resettingAt = $resettingAt;

        return $this;
    }

    /**
     * Resetting token fluent setter.
     *
     * @param string|null $resettingToken new token
     *
     * @return User
     */
    public function setResettingToken(?string $resettingToken): self
    {
        $this->resettingToken = $resettingToken;

        return $this;
    }

    /**
     * Setter of the roles.
     *
     * @param array $roles roles to set
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Terms of service fluent setter.
     *
     * @param bool $tos the new TOS value
     *
     * @return User
     */
    public function setTos(bool $tos): self
    {
        $this->tos = $tos;

        return $this;
    }

    /**
     * Set the username of user.
     *
     * @param string $username the new username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->setMail($username);

        return $this;
    }

    /**
     * Constructs the object.
     *
     * @see http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized the string representation of the user instance
     */
    public function unserialize($serialized): void
    {
        list(
            $this->identifier,
            $this->credit,
            $this->givenName,
            $this->mail,
            $this->name,
            $this->password,
            $this->resettingAt,
            $this->resettingToken,
            $this->roles,
            $this->society,
            $this->type,
            $this->vat
            ) = unserialize($serialized);
    }

    /**
     * Is this user valid?
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context the context to report error
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->isMoral() && empty($this->getSociety())) {
            $context->buildViolation('error.society.blank')
                ->atPath('society')
                ->addViolation()
            ;
        }

        if ($this->isPhysic() && empty($this->getName())) {
            $context->buildViolation('error.name.blank')
                ->atPath('name')
                ->addViolation()
            ;
        }
    }

    /**
     * VAT Getter.
     *
     * @return string|null
     */
    public function getVat(): ?string
    {
        return $this->vat;
    }

    /**
     * VAT Fluent setter.
     *
     * @param string $vat the new VAT in decimal
     *
     * @return $this
     */
    public function setVat(string $vat): self
    {
        $this->vat = $vat;

        return $this;
    }
}
