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

use App\Manager\VatManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Vat update model form.
 *
 * This form defines assertion to change its VAT profile.
 */
class Vat
{
    /**
     * The actual vat percent.
     *
     * @var string
     */
    private $actual = '0.2000';

    /**
     * The explanation to help accountant to decide.
     *
     * @Assert\Length(max="63")
     *
     * @var string|null
     */
    private $explanation;

    /**
     * The vat percent.
     *
     * @Assert\Range(min="0", max="99")
     * @Assert\NotNull
     *
     * @var string
     */
    private $vat;

    /**
     * The actual vat getter.
     */
    public function getActual(): string
    {
        return $this->actual;
    }

    /**
     * Explanation getter.
     */
    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

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
     * The actual vat setter.
     *
     * @param string $actual the customer actual vat
     *
     * @return Vat
     */
    public function setActual(string $actual): self
    {
        $this->actual = $actual;

        return $this;
    }

    /**
     * Explanation fluent setter.
     *
     * @param string|null $explanation the new explanation
     */
    public function setExplanation(?string $explanation): self
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * Mail fluent setter.
     *
     * @param string $vat mail of user who lost its password
     */
    public function setVat(?string $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Is this order valid?
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context the context to report error
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->isVatChanged()) {
            $context->buildViolation('error.vat.same')
                ->addViolation()
            ;
        }

        if (empty($this->getExplanation()) && !$this->isVatDefault()) {
            $message = 'error.vat.empty-europe';
            if ((float) $this->getVat() === (float) VatManagerInterface::DOMTOM_VAT) {
                $message = 'error.vat.empty-domtom';
            }

            $context->buildViolation($message)
                ->addViolation()
            ;
        }
    }

    /**
     * Is the asked VAT different from the customer actual vat?
     */
    private function isVatChanged(): bool
    {
        return $this->getVat() !== $this->getActual();
    }

    /**
     * Is asked vat set to the default value.
     */
    private function isVatDefault(): bool
    {
        return (float) $this->getVat() === (float) VatManagerInterface::DEFAULT_VAT;
    }
}
