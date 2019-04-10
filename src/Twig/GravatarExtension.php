<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Gravatar twig extension.
 */
class GravatarExtension extends AbstractExtension
{
    private $secure_request = false;

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
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
        );
    }

    /**
     * Gravatar Filter.
     *
     * @param string      $email
     * @param string|null $size
     * @param string|null $default
     *
     * @return string
     */
    public function gravatarFilter($email, $size = null, $default = null)
    {
        $defaults = array(
            '404',
            'mm',
            'identicon',
            'monsterid',
            'wavatar',
            'retro',
            'blank',
        );
        $hash = md5($email);
        $url = $this->secure_request ? 'https://' : 'http://';
        $url .= 'www.gravatar.com/avatar/'.$hash;
        // Size
        if (!is_null($size)) {
            $url .= "?s=$size";
        }
        // Default
        if (!is_null($default)) {
            $url .= is_null($size) ? '?' : '&';
            $url .= in_array($default, $defaults) ? $default : urlencode($default);
        }

        return $url;
    }

    /**
     * The request is secured.
     *
     * @param string      $email
     * @param string|null $size
     * @param string|null $default
     *
     * @return string url
     */
    public function secureGravatarFilter($email, $size = null, $default = null)
    {
        $this->secure_request = true;

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
