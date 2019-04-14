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
     * Country getter.
     *
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * Country fluent setter.
     *
     * @param string $country the new country
     *
     * @return self
     */
    public function setCountry(?string $country): self;

    /**
     * Locality getter.
     *
     * @return string|null
     */
    public function getLocality(): ?string;

    /**
     * Locality fluent setter.
     *
     * @param string|null $locality the new locality
     *
     * @return self
     */
    public function setLocality(?string $locality): self;

    /**
     * Post office box number getter.
     *
     * @return string|null
     */
    public function getComplement(): ?string;

    /**
     * Post office box number fluent setter.
     *
     * @param string $complement the address complement
     *
     * @return self
     */
    public function setComplement(string $complement): self;

    /**
     * Postal code getter.
     *
     * @return string|null
     */
    public function getPostalCode(): ?string;

    /**
     * Postal code fluent setter.
     *
     * @param string|null $postalCode the new postal code
     *
     * @return self
     */
    public function setPostalCode(?string $postalCode): self;

    /**
     * Street address getter.
     *
     * @return string|null
     */
    public function getStreetAddress(): ?string;

    /**
     * Street address fluent setter.
     *
     * @param string|null $streetAddress new street address
     *
     * @return self
     */
    public function setStreetAddress(?string $streetAddress): self;
}
