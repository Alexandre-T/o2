<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Sort twig extension.
 */
class SortExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'sort' => new TwigFunction(
                'faSort',
                [$this, 'sortFunction'],
                ['is_safe', ['html']]
            ),
        );
    }

    /**
     * sort Function.
     *
     * @param bool   $sorted
     * @param string $sort
     * @param string $type
     *
     * @return string
     */
    public function sortFunction(bool $sorted = false, string $sort = 'asc', string $type = null)
    {
        $result = 'fa fa-sort';

        if ($sorted) {
            switch ($type) {
                case 'numeric':
                case 'alpha':
                case 'amount':
                    $result .= "-$type";
            }

            $result .= 'desc' == $sort ? '-up' : '-down';
        }

        return $result;
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
}
