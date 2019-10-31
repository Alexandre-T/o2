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

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vat update model form.
 *
 * This form defines assertion to change its VAT profile.
 */
class Vat
{
    /**
     * The vat percent.
     *
     * @Assert\Range(min="0",max="99")
     * @Assert\NotNull
     *
     * @var string
     */
    private $vat;

    /**
     * The explanation to help accountant to decide.
     *
     * @Assert\Length(max="63")
     *
     * @var string|null
     */
    private $explanation;

    /**
     * Vat getter.
     *
     * @return string
     */
    public function getVat(): ?string
    {
        return $this->vat;
    }

    /**
     * Mail fluent setter.
     *
     * @param string $vat mail of user who lost its password
     *
     * @return Vat
     */
    public function setVat(?string $vat): Vat
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Explanation getter.
     *
     * @return string|null
     */
    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    /**
     * Explanation fluent setter.
     *
     * @param string|null $explanation the new explanation.
     *
     * @return Vat
     */
    public function setExplanation(?string $explanation): Vat
    {
        $this->explanation = $explanation;
        return $this;
    }
}
