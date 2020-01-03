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

/**
 * Trait OlsxTrait.
 */
trait OlsxTrait
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $olsxIdentifier;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $olsxStatus = OlsxInterface::UNREGISTERED;

    /**
     * Return OLSX identifier if user is fully registered.
     */
    public function getOlsxIdentifier(): ?int
    {
        return $this->olsxIdentifier;
    }

    /**
     * Is user a personal registered customer of reseller.
     */
    public function isOlsxCustomer(): bool
    {
        return OlsxInterface::REGISTERED === $this->olsxStatus;
    }

    /**
     * Is user currently registering?
     *
     * If user is registered, he is no more registering.
     */
    public function isOlsxRegistering(): bool
    {
        return OlsxInterface::REGISTERING === $this->olsxStatus;
    }

    /**
     * Fluent OLSX identifier.
     *
     * @param int $identifier the customer identifier
     *
     * @return OlsxInterface|OlsxTrait
     */
    public function setOlsxIdentifier(?int $identifier): OlsxInterface
    {
        $this->olsxIdentifier = $identifier;

        return $this;
    }

    /**
     * Fluent registered setter.
     *
     * @return OlsxInterface|OlsxTrait
     */
    public function setRegistered(): OlsxInterface
    {
        $this->olsxStatus = OlsxInterface::REGISTERED;

        return $this;
    }

    /**
     * Fluent registering setter.
     *
     * @return OlsxInterface|OlsxTrait
     */
    public function setRegistering(): OlsxInterface
    {
        $this->olsxStatus = OlsxInterface::REGISTERING;

        return $this;
    }

    /**
     * Fluent unregistered setter.
     *
     * @return OlsxInterface|OlsxTrait
     */
    public function setUnregistered(): OlsxInterface
    {
        $this->olsxStatus = OlsxInterface::UNREGISTERED;

        return $this;
    }
}
