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
    public const ENGLISH = 'gb';

    public const FRENCH = 'fr';

    public const INITIAL = 'fr';

    /**
     * Get the profile language.
     */
    public function getLanguage(): string;

    /**
     * Get the profile locale (fr-FR, en-US).
     */
    public function getLocale(): string;

    /**
     * Is the locale set to English.
     */
    public function isEnglish(): bool;

    /**
     * Is the locale set to French.
     */
    public function isFrench(): bool;

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
