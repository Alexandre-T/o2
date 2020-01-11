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

use Alexandre\EvcBundle\Exception\EvcException;
use Alexandre\EvcBundle\Service\EvcServiceInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Olsx register model form.
 *
 * This form defines assertion to register on olsx service.
 */
class OlsxRegister
{
    /**
     * The personal olsx code of user.
     *
     * @Assert\Range(min="0", max="999999", minMessage="error.olsx-code.min", maxMessage="error.olsx-code.max")
     *
     * @var int
     */
    private $code;

    /**
     * @var EvcServiceInterface
     */
    private $evcService;

    /**
     * OlsxRegisterFormType constructor.
     *
     * @param EvcServiceInterface $evcService the EVC service
     */
    public function __construct(EvcServiceInterface $evcService)
    {
        $this->evcService = $evcService;
    }

    /**
     * Code getter.
     *
     * @return int
     */
    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * Code fluent setter.
     *
     * @param int $code the new code
     *
     * @return OlsxRegister
     */
    public function setCode(?int $code): self
    {
        $this->code = $code;

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
        try {
            if (null !== $this->getCode() && !$this->evcService->exists($this->getCode())) {
                $context->buildViolation('error.olsx.non-existent-customer')
                    ->addViolation();
            }
        } catch (EvcException $exception) {
            $context->buildViolation('error.olsx.unavailable.check-unavailable')
                ->addViolation();
        }
    }
}
