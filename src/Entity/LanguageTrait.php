<?php

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
     * @AppAssert\LanguageValue()
     *
     * @ORM\Column(type="string", length=2, name="usr_language", options={"comment": "Language"})
     *
     * @Gedmo\Versioned
     *
     * @var string the language
     */
    protected $language;

    /**
     * Language getter.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        if (null === $this->language) {
            $this->language = LanguageInterface::DEFAULT;
        }

        return $this->language;
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
