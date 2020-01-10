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

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Sort twig extension.
 */
class SortExtension extends AbstractExtension
{
    /**
     * Declare faSort as a function.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            'sort' => new TwigFunction(
                'faSort',
                [$this, 'sortFunction'],
                ['is_safe', ['html']]
            ),
        ];
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'sort_extension';
    }

    /**
     * Dort function.
     *
     * @param bool   $sorted change icon if sorted
     * @param string $sort   sort order asc or desc
     * @param string $type   type of data
     *
     * @return string
     */
    public function sortFunction(bool $sorted = false, string $sort = 'asc', ?string $type = null)
    {
        $result = 'fas fa-sort';

        if ($sorted) {
            switch ($type) {
                case 'numeric':
                case 'alpha':
                case 'amount':
                    $result .= "-{$type}";
            }

            $result .= 'desc' === $sort ? '-up' : '-down';
        }

        return $result;
    }
}
