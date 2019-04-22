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
trait PostalAddressTrait
{
    /**
     * Postal complement address.
     *
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", length=32, nullable=true, name="pad_complement", options={"comment": "Complement"})
     *
     * @Gedmo\Versioned
     */
    private $complement;

    /**
     * Country.
     *
     * @var string
     *
     * @Assert\Country
     * @Assert\Length(max="2", maxMessage="form.error.country")
     *
     * @ORM\Column(type="string", length=2, name="pad_country", options={"comment": "Country alpha2 code"})
     *
     * @Gedmo\Versioned
     */
    private $country;

    /**
     * Locality.
     *
     * @Assert\NotBlank(message="error.locality.blank")
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", length=32, name="pad_locality", options={"comment": "Locality"})
     *
     * @Gedmo\Versioned
     */
    private $locality;

    /**
     * Postal code.
     *
     * @Assert\NotBlank(message="error.postal-code.blank")
     * @Assert\Length(max="5")
     *
     * @ORM\Column(type="string", length=5, name="pad_code", options={"comment": "Postal code"})
     *
     * @Gedmo\Versioned
     */
    private $postalCode;

    /**
     * Street address.
     *
     * @Assert\NotBlank(message="error.street-address.blank")
     * @Assert\Length(max="32")
     *
     * @ORM\Column(type="string", length=32, name="pad_street", options={"comment": "Street address"})
     *
     * @Gedmo\Versioned
     */
    private $streetAddress;

    /**
     * Copy address from postal address to current object.
     *
     * @param PostalAddressInterface $postalAddress postal address interface to copy
     *
     * @return PostalAddressInterface|PostalAddressTrait
     */
    public function copyAddress(PostalAddressInterface $postalAddress): PostalAddressInterface
    {
        $this->setComplement($postalAddress->getComplement());
        $this->setCountry($postalAddress->getCountry());
        $this->setLocality($postalAddress->getLocality());
        $this->setPostalCode($postalAddress->getPostalCode());
        $this->setStreetAddress($postalAddress->getStreetAddress());

        return $this;
    }

    /**
     * Post office box number getter.
     *
     * @return string|null
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }

    /**
     * Country getter.
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Locality getter.
     *
     * @return string|null
     */
    public function getLocality(): ?string
    {
        return $this->locality;
    }

    /**
     * Postal code getter.
     *
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * Street address getter.
     *
     * @return string|null
     */
    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    /**
     * Post office box number fluent setter.
     *
     * @param string $complement the address complement
     *
     * @return PostalAddressInterface|PostalAddressTrait
     */
    public function setComplement(?string $complement): PostalAddressInterface
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * Country fluent setter.
     *
     * @param string|null $country the new country
     *
     * @return PostalAddressInterface|PostalAddressTrait
     */
    public function setCountry(?string $country): PostalAddressInterface
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Locality fluent setter.
     *
     * @param string|null $locality the new locality
     *
     * @return PostalAddressInterface|PostalAddressTrait
     */
    public function setLocality(?string $locality): PostalAddressInterface
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Postal code fluent setter.
     *
     * @param string|null $postalCode the new postal code
     *
     * @return PostalAddressInterface|PostalAddressTrait
     */
    public function setPostalCode(?string $postalCode): PostalAddressInterface
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Street address fluent setter.
     *
     * @param string|null $streetAddress new street address
     *
     * @return PostalAddressInterface|PostalAddressTrait
     */
    public function setStreetAddress(?string $streetAddress): PostalAddressInterface
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }
}
