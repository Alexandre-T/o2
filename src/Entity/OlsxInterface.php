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
 * Interface OlsxInterface.
 */
interface OlsxInterface
{
    public const REGISTERED = 2;
    public const REGISTERING = 1;
    public const UNREGISTERED = 0;

    /**
     * Return OLSX identifier if user is fully registered.
     */
    public function getOlsxIdentifier(): ?int;

    /**
     * Is user a personal registered customer of reseller.
     */
    public function isOlsxCustomer(): bool;

    /**
     * Is user currently registering?
     *
     * If user is registered, he is no more registering.
     */
    public function isOlsxRegistering(): bool;

    /**
     * Fluent OLSX identifier.
     *
     * @param int $identifier the customer identifier
     *
     * @return $this
     */
    public function setOlsxIdentifier(int $identifier): self;

    /**
     * Fluent registered setter.
     *
     * @return $this
     */
    public function setRegistered(): self;

    /**
     * Fluent registering setter.
     *
     * @return $this
     */
    public function setRegistering(): self;
}
