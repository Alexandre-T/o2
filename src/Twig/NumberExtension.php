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

use App\Entity\LanguageInterface;
use Locale;
use NumberFormatter;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NumberExtension extends AbstractExtension
{
    /**
     * Twig environment to forward it to intl extension.
     *
     * @var Environment
     */
    private $environment;

    /**
     * The locale coming from session or user language.
     *
     * @var string
     */
    private $locale;

    /**
     * Number Extension constructor.
     *
     * @param TokenStorageInterface $tokenStorage the token storage to retrieve the user locale
     * @param SessionInterface      $session      the session to retrieve the session locale
     * @param Environment           $env          the twig environment
     */
    public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session, Environment $env)
    {
        $this->environment = $env;
        $this->locale = Locale::getDefault();
        $token = $tokenStorage->getToken();

        if ($session->has('_locale')) {
            $this->locale = $this->convert($session->get('_locale'));
        }

        if ($token instanceof TokenInterface) {
            $user = $token->getUser();
            if ($user instanceof LanguageInterface) {
                $this->locale = $this->convert($user->getLanguage());
            }
        }
    }

    /**
     * Format a localized currency. It override the locale with user language if not provided.
     *
     * @param mixed       $number   the number to format
     * @param string|null $currency the currency to format
     * @param string|null $locale   the locale or user language if not provided
     *
     * @return string
     */
    public function currencyFilter($number, string $currency = 'EUR', string $locale = null): string
    {
        $locale = $this->getLocale($locale);

        return twig_localized_currency_filter($number, $currency, $locale);
    }

    /**
     * The date filter.
     *
     * @param mixed  $date       the date to convert
     * @param string $dateFormat the date format
     * @param string $timeFormat the time format
     * @param null   $locale     the optional locale will replace current locale when provided
     * @param null   $timezone   the timezone
     * @param null   $format     the format
     * @param string $calendar   the calendar
     *
     * @return bool|false|string
     */
    public function dateFilter(
     $date,
     $dateFormat = 'medium',
     $timeFormat = 'medium',
     $locale = null,
     $timezone = null,
     $format = null,
     $calendar = 'gregorian'
    ): string {
        $locale = $this->getLocale($locale);

        return twig_localized_date_filter(
            $this->environment,
            $date,
            $dateFormat,
            $timeFormat,
            $locale,
            $timezone,
            $format,
            $calendar
        );
    }

    /**
     * Declare setting as filter.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            'euro' => new TwigFilter(
                'euro',
                [$this, 'currencyFilter'],
                []
            ),

            'date' => new TwigFilter(
                'localizeddate',
                [$this, 'dateFilter'],
                []
            ),

            'number' => new TwigFilter(
                'localizednumber',
                [$this, 'numberFilter'],
                []
            ),

            'percent' => new TwigFilter(
                'localizedpercent',
                [$this, 'percentFilter'],
                []
            ),

            'vat' => new TwigFilter(
                'vat',
                [$this, 'percentFilter'],
                []
            ),
        ];
    }

    /**
     * Return name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'app_percent_extension';
    }

    /**
     * Format a localized number. It override the locale with user language if not provided.
     *
     * @param mixed       $number the number to format
     * @param string      $style  the style
     * @param string      $type   the type
     * @param string|null $locale the locale or user language if not provided
     *
     * @throws SyntaxError by twig intl extension
     *
     * @return string
     */
    public function numberFilter(
     $number,
     string $style = 'decimal',
     string $type = 'default',
     string $locale = null
    ): string {
        $locale = $this->getLocale($locale);

        return twig_localized_number_filter($number, $style, $type, $locale);
    }

    /**
     * Percent filter.
     *
     * @param mixed       $number   the number to convert
     * @param string      $type     the type of the number
     * @param int         $decimals the number of decimals
     * @param string|null $locale   the locale
     *
     * @return string
     */
    public function percentFilter($number, string $type = 'default', int $decimals = 2, string $locale = null): string
    {
        $locale = null !== $locale ? $locale : $this->locale;

        $formatter = NumberFormatter::create($locale, NumberFormatter::PERCENT);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);

        static $typeValues = [
            'default' => NumberFormatter::TYPE_DEFAULT,
            'int32' => NumberFormatter::TYPE_INT32,
            'int64' => NumberFormatter::TYPE_INT64,
            'double' => NumberFormatter::TYPE_DOUBLE,
            'currency' => NumberFormatter::TYPE_CURRENCY,
        ];

        if (!isset($typeValues[$type])) {
            $type = 'default';
        }

        return $formatter->format($number, $typeValues[$type]);
    }

    /**
     * Convert gb to en-gb and others to fr-fr.
     *
     * @param string $language the code language
     *
     * @return string
     */
    private function convert(string $language): string
    {
        switch ($language) {
            case LanguageInterface::ENGLISH:
                return 'en-gb';
        }

        return 'fr-fr';
    }

    /**
     * Return the current locale if locale parameter is null.
     *
     * @param string|null $locale the optional locale
     *
     * @return string
     */
    private function getLocale(string $locale = null): string
    {
        if (null === $locale) {
            return $this->locale;
        }

        return $locale;
    }
}
