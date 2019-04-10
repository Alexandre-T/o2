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
use Twig\TwigFilter;

/**
 * Gravatar twig extension.
 */
class GravatarExtension extends AbstractExtension
{
    private $securedRequest = false;

    /**
     * Declare sGravatar and gravatar as filter.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            'gravatar' => new TwigFilter(
                'gravatarFilter',
                [$this, 'gravatarFilter'],
                ['is_safe', ['html']]
            ),
            'sgravatar' => new TwigFilter(
                'securedGravatarFilter',
                [$this, 'securedGravatarFilter'],
                ['is_safe', ['html']]
            ),
        ];
    }

    /**
     * Gravatar Filter.
     *
     * @param string      $email   Email for gravatar
     * @param string|null $size    Size wanted
     * @param string|null $default default parameters
     *
     * @return string
     */
    public function gravatarFilter($email, $size = null, $default = null)
    {
        $defaults = [
            '404',
            'mm',
            'identicon',
            'monsterid',
            'wavatar',
            'retro',
            'blank',
        ];
        $hash = md5($email);
        $url = $this->securedRequest ? 'https://' : 'http://';
        $url .= 'www.gravatar.com/avatar/'.$hash;

        // Size
        if (null !== $size) {
            $url .= "?s=${size}";
        }

        // Default
        if (null !== $default) {
            $url .= null === $size ? '?' : '&';
            $url .= in_array($default, $defaults) ? $default : urlencode($default);
        }

        return $url;
    }

    /**
     * The request is secured.
     *
     * @param string      $email   Email for gravatar
     * @param string|null $size    Size wanted
     * @param string|null $default default parameters
     *
     * @return string url
     */
    public function secureGravatarFilter($email, $size = null, $default = null)
    {
        $this->securedRequest = true;

        return $this->gravatarFilter($email, $size, $default);
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'gravatar_extension';
    }
}
