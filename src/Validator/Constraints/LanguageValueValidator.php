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

namespace App\Validator\Constraints;

use App\Entity\LanguageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * A validator to check the language value
 */
class LanguageValueValidator extends ConstraintValidator
{
    /**
     * Validate value.
     *
     * @param string|mixed $value      the value to validate
     * @param Constraint   $constraint the constraint
     *
     * @throws UnexpectedTypeException  when $constraint is not an instance of LanguageValue
     * @throws UnexpectedValueException when $value is not a string
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LanguageValue) {
            throw new UnexpectedTypeException($constraint, LanguageValue::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        if (LanguageInterface::FRENCH !== $value && LanguageInterface::ENGLISH !== $value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation()
            ;
        }
    }
}
