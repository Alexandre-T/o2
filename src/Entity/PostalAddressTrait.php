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
/**
 * This file is part of the o2 Application.
 *
 * PHP version 7.2
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     * Country getter.
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Country fluent setter.
     *
     * @param string $country the new country
     *
     * @return self
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
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
     * Locality fluent setter.
     *
     * @param string|null $locality the new locality
     *
     * @return self
     */
    public function setLocality(?string $locality): self
    {
        $this->locality = $locality;

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
     * Post office box number fluent setter.
     *
     * @param string $complement the address complement
     *
     * @return self
     */
    public function setComplement(string $complement): self
    {
        $this->complement = $complement;

        return $this;
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
     * Postal code fluent setter.
     *
     * @param string|null $postalCode the new postal code
     *
     * @return self
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
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
     * Street address fluent setter.
     *
     * @param string|null $streetAddress new street address
     *
     * @return self
     */
    public function setStreetAddress(?string $streetAddress): self
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }
}
