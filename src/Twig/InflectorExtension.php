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

use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

/**
 * Inflector twig extension.
 */
class InflectorExtension extends AbstractExtension
{
    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor sets the translator.
     *
     * @param TranslatorInterface $translator dependency injection
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return true only when value is strictly an array.
     *
     * @param mixed $value value to test
     */
    public function arrayTest($value): bool
    {
        return is_array($value);
    }

    /**
     * Returns given word as CamelCased.
     *
     * Converts a word like "send_email" to "SendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "WhoSOnline"
     *
     * @see variablize
     *
     * @param string|null $word Word to convert to camel case
     *
     * @return string UpperCamelCasedWord
     */
    public function camelizeFilter(?string $word): string
    {
        return str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/', ' ', $word)));
    }

    /**
     * Country filter convert country alpha code to country name under locale language.
     *
     * @param string|null $country the country code filter
     */
    public function countryFilter(?string $country): string
    {
        if (empty($country)) {
            return '';
        }

        try {
            return Countries::getName($country);
        } catch (MissingResourceException $exception) {
            return '';
        }
    }

    /**
     * Returns word or not available text if empty.
     *
     * @param mixed $word Word
     *
     * @return string UpperCamelCasedWord
     */
    public function emptyizeFilter($word): string
    {
        if (is_array($word)) {
            return '';
        }

        return empty($word) ? $this->translator->trans('empty') : (string) $word;
    }

    /**
     * List of filters.
     */
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('camelize', [$this, 'camelizeFilter'], ['is_safe' => ['html']]),
            new TwigFilter('country', [$this, 'countryFilter']),
            new TwigFilter('emptyize', [$this, 'emptyizeFilter'], ['is_safe' => ['html']]),
            new TwigFilter('hyphenize', [$this, 'hyphenizeFilter'], ['is_safe' => ['html']]),
            new TwigFilter('titleize', [$this, 'titleizeFilter'], ['is_safe' => ['html']]),
            new TwigFilter('underscorize', [$this, 'underscorizeFilter'], ['is_safe' => ['html']]),
            new TwigFilter('variablize', [$this, 'variablizeFilter'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * List of tests.
     */
    public function getTests(): array
    {
        return [
            new TwigTest('array', [$this, 'arrayTest']),
        ];
    }

    /**
     * Returns a human-readable string from $word.
     *
     * Returns a human-readable string from $word, by replacing
     * underscores with a space, and by upper-casing the initial
     * character by default.
     *
     * If you need to uppercase all the words you just have to
     * pass 'all' as a second parameter.
     *
     * @param string $word      String to "humanize"
     * @param string $uppercase if set to 'all' it will uppercase all the words
     *                          instead of just the first one
     *
     * @return string Human-readable word
     */
    public function humanizeFilter($word, $uppercase = '')
    {
        $uppercase = 'all' === $uppercase ? 'ucwords' : 'ucfirst';

        return $uppercase(str_replace('_', ' ', preg_replace('/_id$/', '', $word)));
    }

    /**
     * Converts a word "into-it-s-hyphenated-version".
     *
     * Convert any "CamelCased" or "ordinary Word" into an
     * "hyphenated-word".
     *
     * This can be really useful for creating friendly URLs.
     *
     * @param string|null $value data to hyphen
     *
     * @return string hyphenized word
     */
    public function hyphenizeFilter(?string $value): string
    {
        $regex = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1-\2', $value ?? '');
        $regex = preg_replace('/([a-z])([A-Z])/', '\1-\2', $regex);
        $regex = preg_replace('/([0-9])([A-Z])/', '\1-\2', $regex);
        $regex = preg_replace('/[^A-Z^a-z^0-9]+/', '-', $regex);

        return strtolower($regex);
    }

    /**
     * Converts an underscored or CamelCase word into a English
     * sentence.
     *
     * The titleize public function converts text like "WelcomePage",
     * "welcome_page" or  "welcome page" to this "Welcome
     * Page".
     * If second parameter is set to 'first' it will only
     * capitalize the first character of the title.
     *
     * @param string $word      Word to format as tile
     * @param string $uppercase If set to 'first' it will only uppercase the
     *                          first character. Otherwise it will uppercase all
     *                          the words in the title.
     *
     * @return string Text formatted as title
     */
    public function titleizeFilter($word, $uppercase = '')
    {
        $uppercase = 'first' === $uppercase ? 'ucfirst' : 'ucwords';

        return $uppercase($this->humanizeFilter($this->underscorizeFilter($word)));
    }

    /**
     * Converts a word "into_it_s_underscored_version".
     *
     * Convert any "CamelCased" or "ordinary Word" into an
     * "underscored_word".
     *
     * This can be really useful for creating friendly URLs.
     *
     * @param string $word Word to underscore
     *
     * @return string Underscored word
     */
    public function underscorizeFilter($word)
    {
        $regex = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', $word);
        $regex = preg_replace('/([a-zd])([A-Z])/', '\1_\2', $regex);
        $regex = preg_replace('/[^A-Z^a-z^0-9]+/', '_', $regex);

        return strtolower($regex);
    }

    /**
     * Same as camelize but first char is underscored.
     *
     * Converts a word like "send_email" to "sendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "whoSOnline"
     *
     * @see camelizeFilter
     *
     * @param string $word Word to lowerCamelCase
     *
     * @return string Returns a lowerCamelCasedWord
     */
    public function variablizeFilter(string $word): string
    {
        $word = $this->camelizeFilter($word);

        return strtolower($word[0]).substr($word, 1);
    }
}
