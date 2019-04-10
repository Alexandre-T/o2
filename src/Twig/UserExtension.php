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

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

/**
 * User twig extension.
 */
class UserExtension extends AbstractExtension
{
    /**
     * List of filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
        ];
    }

    /**
     * List of tests.
     *
     * @return array
     */
    public function getTests(): array
    {
        return [
            new TwigTest('society', [$this, 'societyTest']),
        ];
    }

    /**
     * Return true only when value is an user AND user is a society.
     *
     * @param mixed $value value to evaluate
     *
     * @return bool
     */
    public function societyTest($value): bool
    {
        if ($value instanceof User) {
            return $value->isSociety();
        }

        if (true === $value) {
            return true;
        }

        return false;
    }
}
