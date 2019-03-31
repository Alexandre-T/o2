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
use Gedmo\Mapping\Annotation as Gedmo;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(
 *     name="ts_user",
 *     schema="data",
 *     options={"comment": "Users data table"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_user_mail",  columns={"usr_mail"})
 *     }
 * )
 *
 * @Gedmo\Loggable
 *
 * @UniqueEntity("mail",  message="error.user.mail.unique")
 */
class User implements GedmoInterface, UserInterface, Serializable
{
    /**
     * This is a moral person.
     */
    public const MORAL = false;

    /**
     * This is a physic person.
     */
    public const PHYSIC = true;

    /**
     * Available types.
     */
    public const TYPES = [self::MORAL, self::PHYSIC];

    /**
     * Each available roles.
     */
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_COMPTABLE = 'ROLE_COMPTABLE';
    public const ROLE_PROGRAMMER = 'ROLE_PROGRAMMER';
    public const ROLE_USER = 'ROLE_USER';

    /**
     * Initial roles.
     */
    public const INITIAL_ROLES = [self::ROLE_USER];

    /**
     * Identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="usr_id", options={"unsigned": true, "comment": "User identifier"})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Credits owned by user.
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="usr_credit", options={"unsigned": true, "comment": "User credits"})
     */
    private $credit = 0;

    /**
     * Given name.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true, name="usr_given", options={"comment": "User given name"})
     */
    private $givenName;

    /**
     * User mail and identifier.
     *
     * @var string
     * @Assert\Length(max="255")
     *
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Email
     *
     * @ORM\Column(type="string", unique=true, length=255, name="usr_mail", options={"comment": "User mail"})
     * @Gedmo\Versioned
     */
    private $mail;

    /**
     * Name.
     *
     * @var string
     *
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", length=32, nullable=true, name="usr_name", options={"comment": "User name"}))
     */
    private $name;

    /**
     * User encoded password.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=128, name="usr_password", options={"comment": "Encrypted password"})
     * @Gedmo\Versioned
     */
    private $password;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * Roles of this user.
     *
     * @var array
     *
     * @Assert\Count(
     *     min=1,
     *     minMessage="form.error.roles.empty"
     * )
     *
     * @ORM\Column(type="json_array", nullable=false, name="usr_roles", options={"comment": "User roles"})
     *
     * @Gedmo\Versioned
     */
    private $roles = self::INITIAL_ROLES;

    /**
     * Society name.
     *
     * @var string
     *
     * @Assert\Length(max="64")
     * @ORM\Column(type="string", length=64, nullable=true, name="usr_society", options={"comment": "User society"})
     */
    private $society;

    /**
     * Moral or physic person.
     *
     * @var bool
     *
     * @Assert\Choice(choices=User::TYPES, message="form.error.types.choices")
     * @Assert\NotNull
     *
     * @ORM\Column(type="boolean", name="usr_type", options={"comment": "User mail"})
     */
    private $type = self::PHYSIC;

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
     * Credit getter.
     *
     * @return int
     */
    public function getCredit(): int
    {
        return $this->credit;
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
     * Given name getter.
     *
     * @return string|null
     */
    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    /**
     * Given name fluent setter.
     *
     * @param string|null $givenName new given name
     *
     * @return User
     */
    public function setGivenName(?string $givenName): self
    {
        $this->givenName = $givenName;

        return $this;
    }

    /**
     * Id getter.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Label getter.
     *
     * @return string
     */
    public function getLabel(): string
    {
        if ($this->isMoral()) {
            return (string) $this->getSociety();
        }

        if (empty($this->getGivenName())) {
            return (string) $this->getName();
        }

        if (empty($this->getName())) {
            return (string) $this->getGivenName();
        }

        return $this->getGivenName().' '.$this->getName();
    }

    /**
     * Is this a moral person?
     *
     * @return bool
     */
    public function isMoral(): bool
    {
        return self::MORAL === $this->type;
    }

    /**
     * Name getter.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Name fluent setter.
     *
     * @param string|null $name new name
     *
     * @return User
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
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
     * Return the non-persistent plain password.
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
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
     * To implements UserInterface.
     *
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Society name getter.
     *
     * @return string|null
     */
    public function getSociety(): ?string
    {
        return $this->society;
    }

    /**
     * Society name fluent setter.
     *
     * @param string|null $society new society name
     *
     * @return User
     */
    public function setSociety(?string $society): self
    {
        $this->society = $society;

        return $this;
    }

    /**
     * Return type.
     *
     * @return bool|null
     */
    public function getType(): bool
    {
        return $this->type;
    }

    /**
     * Type fluent setter.
     *
     * @param bool $type new type
     *
     * @return User
     */
    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Return the username of user.
     * Username is the mail in this application.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->getMail();
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
     * Setter of the mail.
     *
     * @param string $mail new mail
     *
     * @return User
     */
    public function setMail(string $mail): User
    {
        $this->mail = $mail;

        return $this;
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
     * Is this user an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Is this user a client.
     *
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->hasRole(self::ROLE_USER);
    }

    /**
     * Is this user a comptable.
     *
     * @return bool
     */
    public function isComptable(): bool
    {
        return $this->hasRole(self::ROLE_COMPTABLE);
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
                $this->id,
                $this->credit,
                $this->givenName,
                $this->mail,
                $this->name,
                $this->roles,
                $this->society,
                $this->type,
            ]
        );
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
            $this->id,
            $this->credit,
            $this->givenName,
            $this->mail,
            $this->name,
            $this->roles,
            $this->society,
            $this->type
            ) = unserialize($serialized);
    }
}
