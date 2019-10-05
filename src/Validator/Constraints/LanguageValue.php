<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * The annotation code just below is necessary.
 * Otherwise, symfony will throw an exception.
 *
 * @Annotation
 */
class LanguageValue extends Constraint
{
    public $message = 'error.language.unknown';

    /**
     * Validation function.
     *
     * @return string
     */
    public function validatedBy(): string
    {
        return \get_class($this).'Validator';
    }
}