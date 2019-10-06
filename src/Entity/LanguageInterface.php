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

namespace App\Entity;

/**
 * Language interface.
 */
interface LanguageInterface
{
    public const DEFAULT = self::FRENCH;
    public const ENGLISH = 'gb';
    public const FRENCH = 'fr';

    /**
     * Get the profile language.
     *
     * @return string
     */
    public function getLanguage(): string;

    /**
     * Language fluent setter.
     *
     * @param string $language new language
     *
     * @see https://www.iso.org/obp/ui/
     *
     * @return LanguageInterface
     */
    public function setLanguage(string $language): self;
}