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
     * VAT Number.
     *
     * @Assert\Length(max=32)
     *
     * @ORM\Column(type="string", name="per_vat", length=32, nullable=true, options={"comment": "VAT number"})
     *
     * @Gedmo\Versioned
     *
     * TODO rename to vatNumber
     */
    private $vatNumber;

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
     * Name getter.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
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
     * Return type.
     *
     * @return bool|null
     */
    public function getType(): ?bool
    {
        return $this->type;
    }

    /**
     * VAT number getter.
     *
     * @return string|null
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * Is this a moral person?
     *
     * @return bool
     */
    public function isMoral(): bool
    {
        return ConstantInterface::MORAL === $this->type;
    }

    /**
     * Is this a physic person?
     *
     * @return bool
     */
    public function isPhysic(): bool
    {
        return ConstantInterface::PHYSIC === $this->type;
    }

    /**
     * Is this user a society?
     *
     * @return bool
     */
    public function isSociety(): bool
    {
        return ConstantInterface::MORAL === $this->type;
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
     * Type fluent setter.
     *
     * @param bool $type new type
     *
     * @return User
     */
    public function setType(?bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * TVA number fluent setter.
     *
     * @param string|null $vatNumber the new tva number
     *
     * @return User
     */
    public function setVatNumber(?string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }
}
