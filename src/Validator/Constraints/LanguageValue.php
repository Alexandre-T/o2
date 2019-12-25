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

use function get_class;
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
     */
    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }
}
