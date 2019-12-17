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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Postal address trait.
 */
trait PersonTrait
{
    /**
     * Given name.
     *
     * @var string
     *
     * @Assert\Length(max=32)
     *
     * @ORM\Column(type="string", length=32, nullable=true, name="per_given", options={"comment": "Given name"})
     *
     * @Gedmo\Versioned
     */
    private $givenName;

    /**
     * Name.
     *
     * @var string
     *
     * @Assert\Length(max=32)
     *
     * @ORM\Column(type="string", length=32, nullable=true, name="per_name", options={"comment": "Name"}))
     *
     * @Gedmo\Versioned
     */
    private $name;

    /**
     * Society name.
     *
     * @var string
     *
     * @Assert\Length(max=64)
     *
     * @ORM\Column(type="string", length=64, nullable=true, name="per_society", options={"comment": "Society name"})
     *
     * @Gedmo\Versioned
     */
    private $society;

    /**
     * Telephone number.
     *
     * @Assert\Length(max=21)
     *
     * @ORM\Column(type="string", name="usr_phone", length=21, nullable=true, options={"comment": "User phone"})
     *
     * @Gedmo\Versioned
     */
    private $telephone;

    /**
     * Moral or physic person.
     *
     * @var bool
     *
     * @Assert\Choice(choices=PersonInterface::TYPES, message="form.error.types.choices")
     *
     * @ORM\Column(type="boolean", name="per_type", options={"comment": "Morale or physic"})
     *
     * @Gedmo\Versioned
     */
    private $type = PersonInterface::PHYSIC;

    /**
     * VAT Number.
     *
     * @Assert\Length(max=32)
     *
     * @ORM\Column(type="string", name="per_vat", length=32, nullable=true, options={"comment": "VAT number"})
     *
     * @Gedmo\Versioned
     */
    private $vatNumber;

    /**
     * Copy data about identity.
     *
     * @param PersonInterface $person person to copy
     *
     * @return PersonTrait|PersonInterface
     */
    public function copyIdentity(PersonInterface $person): self
    {
        $this->setGivenName($person->getGivenName());
        $this->setName($person->getName());
        $this->setSociety($person->getSociety());
        $this->setTelephone($person->getTelephone());
        $this->setType($person->getType());
        $this->setVatNumber($person->getVatNumber());

        return $this;
    }

    /**
     * Given name getter.
     */
    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    /**
     * Label getter.
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
     * Name getter.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Society name getter.
     */
    public function getSociety(): ?string
    {
        return $this->society;
    }

    /**
     * Telephone number getter.
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Return type.
     */
    public function getType(): ?bool
    {
        return $this->type;
    }

    /**
     * VAT number getter.
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * Is this a moral person?
     */
    public function isMoral(): bool
    {
        return PersonInterface::MORAL === $this->type;
    }

    /**
     * Is this a physic person?
     */
    public function isPhysic(): bool
    {
        return PersonInterface::PHYSIC === $this->type;
    }

    /**
     * Is this user a society?
     */
    public function isSociety(): bool
    {
        return PersonInterface::MORAL === $this->type;
    }

    /**
     * Given name fluent setter.
     *
     * @param string|null $givenName new given name
     *
     * @return PersonTrait|PersonInterface
     */
    public function setGivenName(?string $givenName): PersonInterface
    {
        $this->givenName = $givenName;

        return $this;
    }

    /**
     * Name fluent setter.
     *
     * @param string|null $name new name
     *
     * @return PersonTrait|PersonInterface
     */
    public function setName(?string $name): PersonInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Society name fluent setter.
     *
     * @param string|null $society new society name
     *
     * @return PersonTrait|PersonInterface
     */
    public function setSociety(?string $society): PersonInterface
    {
        $this->society = $society;

        return $this;
    }

    /**
     * Telephone number fluent setter.
     *
     * @param string|null $telephone the new telephone number
     *
     * @return PersonTrait|PersonInterface
     */
    public function setTelephone(?string $telephone): PersonInterface
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Type fluent setter.
     *
     * @param bool $type new type
     *
     * @return PersonTrait|PersonInterface
     */
    public function setType(?bool $type): PersonInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * TVA number fluent setter.
     *
     * @param string|null $vatNumber the new tva number
     *
     * @return PersonTrait|PersonInterface
     */
    public function setVatNumber(?string $vatNumber): PersonInterface
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }
}
