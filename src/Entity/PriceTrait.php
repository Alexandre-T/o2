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

/**
 * Price trait.
 */
trait PriceTrait
{
    /**
     * Price without taxes.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Gedmo\Versioned
     */
    private $price;

    /**
     * VAT price in euro.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     *
     * @Gedmo\Versioned
     */
    private $vat;

    /**
     * Copy price properties from another price interface to current one.
     *
     * @param PriceInterface $price the price interface to copy
     *
     * @return PriceTrait|PriceInterface
     */
    public function copyPrice(PriceInterface $price): self
    {
        $this->setPrice($price->getPrice());
        $this->setVat($price->getVat());

        return $this;
    }

    /**
     * Amount getter.
     *
     * @return float
     */
    public function getAmount(): float
    {
        return (float) $this->price + (float) $this->vat;
    }

    /**
     * Price getter.
     *
     * @return float|string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * VAT in euro.
     *
     * @return float|string
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * Price fluent setter.
     *
     * @param float|string $price new price
     *
     * @return PriceTrait|PriceInterface
     */
    public function setPrice($price): PriceInterface
    {
        $this->price = $price;

        return $this;
    }

    /**
     * VAT fluent setter.
     *
     * @param float|float|string $vat new vat price
     *
     * @return PriceInterface|PriceTrait
     */
    public function setVat($vat): PriceInterface
    {
        $this->vat = $vat;

        return $this;
    }
}
