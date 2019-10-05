<?php

namespace App\Entity;

/**
 * Language interface.
 */
interface LanguageInterface
{
    const DEFAULT = 'FR';

    /**
     * Get the profile language.
     *
     * @return string
     */
    public function getLanguage(): string;

    /**
     * Language fluent setter.
     *
     * @param string $language new language.

     * @see https://www.iso.org/obp/ui/
     *
     * @return LanguageInterface
     */
    public function setLanguage(string $language): self;
}