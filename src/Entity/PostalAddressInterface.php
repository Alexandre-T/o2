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
 * Postal address interface.
 */
interface PostalAddressInterface
{
    /**
     * Post office box number getter.
     */
    public function getComplement(): ?string;

    /**
     * Country getter.
     */
    public function getCountry(): ?string;

    /**
     * Locality getter.
     */
    public function getLocality(): ?string;

    /**
     * Postal code getter.
     */
    public function getPostalCode(): ?string;

    /**
     * Street address getter.
     */
    public function getStreetAddress(): ?string;

    /**
     * Post office box number fluent setter.
     *
     * @param string $complement the address complement
     */
    public function setComplement(string $complement): self;

    /**
     * Country fluent setter.
     *
     * @param string $country the new country
     */
    public function setCountry(?string $country): self;

    /**
     * Locality fluent setter.
     *
     * @param string|null $locality the new locality
     */
    public function setLocality(?string $locality): self;

    /**
     * Postal code fluent setter.
     *
     * @param string|null $postalCode the new postal code
     */
    public function setPostalCode(?string $postalCode): self;

    /**
     * Street address fluent setter.
     *
     * @param string|null $streetAddress new street address
     */
    public function setStreetAddress(?string $streetAddress): self;
}
