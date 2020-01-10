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

use App\Validator\Constraints as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Language trait.
 */
trait LanguageTrait
{
    /**
     * Language.
     *
     * @Assert\NotBlank(message="error.language.blank")
     * @AppAssert\LanguageValue
     *
     * @ORM\Column(type="string", length=2, name="usr_language", options={"comment": "Language"})
     *
     * @Gedmo\Versioned
     *
     * @var string the language
     */
    protected $language = LanguageInterface::INITIAL;

    /**
     * Language getter.
     */
    public function getLanguage(): string
    {
        if (null === $this->language) {
            $this->language = LanguageInterface::INITIAL;
        }

        return $this->language;
    }

    /**
     * Locale getter.
     * Return fr-FR or en-US.
     */
    public function getLocale(): string
    {
        switch ($this->getLanguage()) {
            case LanguageInterface::ENGLISH:
                return 'en-GB';
            default:
                return 'fr-FR';
        }
    }

    /**
     * Is the locale set to English.
     */
    public function isEnglish(): bool
    {
        return LanguageInterface::ENGLISH === $this->getLanguage();
    }

    /**
     * Is the locale set to French.
     */
    public function isFrench(): bool
    {
        return LanguageInterface::FRENCH === $this->getLanguage();
    }

    /**
     * Language fluent setter.
     *
     * @param string $language the new language alpha2 code
     *
     * @return LanguageInterface|LanguageTrait
     */
    public function setLanguage(string $language): LanguageInterface
    {
        $this->language = $language;

        return $this;
    }
}
