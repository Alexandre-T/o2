<?php

namespace App\Twig;

use Symfony\Component\Intl\Intl;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

/**
 * Inflector twig extension.
 *
 * TODO realize tests.
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
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * List of filters.
     *
     * @return array
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
     *
     * @return array
     */
    public function getTests(): array
    {
        return [
            new TwigTest('array', [$this, 'arrayTest']),
        ];
    }

    /**
     * Return true only when value is strictly an array.
     *
     * @param $value
     *
     * @return bool
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
     * @param string|null $country
     *
     * @return string
     */
    public function countryFilter(?string $country): string
    {
        if (empty($country)) {
            return '';
        }

        return Intl::getRegionBundle()->getCountryName($country);
    }

    /**
     * Returns word or not available text if empty.
     *
     * @param string|null $word Word
     *
     * @return string UpperCamelCasedWord
     */
    public function emptyizeFilter(?string $word): string
    {
        return empty($word) ? $this->translator->trans('empty') : $word;
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
        $uppercase = 'all' == $uppercase ? 'ucwords' : 'ucfirst';

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
     * @param string|null $value
     *
     * @return string hyphenized word
     */
    public function hyphenizeFilter(?string $value): string
    {
        $regex1 = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1-\2', $value ?? '');
        $regex2 = preg_replace('/([a-z])([A-Z])/', '\1-\2', $regex1);
        $regex3 = preg_replace('/([0-9])([A-Z])/', '\1-\2', $regex2);
        $regex4 = preg_replace('/[^A-Z^a-z^0-9]+/', '-', $regex3);

        return strtolower($regex4);
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
        $uppercase = 'first' == $uppercase ? 'ucfirst' : 'ucwords';

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
        $regex1 = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\1_\2', $word);
        $regex2 = preg_replace('/([a-zd])([A-Z])/', '\1_\2', $regex1);
        $regex3 = preg_replace('/[^A-Z^a-z^0-9]+/', '_', $regex2);

        return strtolower($regex3);
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
