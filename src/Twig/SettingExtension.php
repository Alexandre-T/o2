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

use App\Model\ServiceStatusInterface;
use DateTimeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SettingExtension extends AbstractExtension
{
    /**
     * Twig environment.
     *
     * @var Environment
     */
    private $env;
    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * SettingExtension constructor.
     *
     * @param TranslatorInterface $translator  provided by injection dependency
     * @param Environment         $environment Twig environment
     */
    public function __construct(TranslatorInterface $translator, Environment $environment)
    {
        $this->translator = $translator;
        $this->env = $environment;
    }

    /**
     * Declare setting as filter.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            'setting' => new TwigFilter(
                'setting',
                [$this, 'settingFilter'],
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
        return 'app_setting_extension';
    }

    /**
     * Setting filter.
     *
     * @param mixed $value the value from setting
     * @param mixed $code
     *
     * @return string
     */
    public function settingFilter($value, $code): string
    {
        if ($value instanceof DateTimeInterface) {
            switch ($code) {
                case 'service-until':
                    return twig_localized_date_filter($this->env, $value, 'long', 'none');
            }

            return twig_localized_date_filter($this->env, $value);
        }

        switch ($code) {
            case 'service-status':
                return $this->getServiceStatus($value);
        }

        return (string) $value;
    }

    /**
     * Service status translator.
     *
     * @param int $value
     *
     * @return string the current status
     */
    private function getServiceStatus(int $value): string
    {
        switch ($value) {
            case ServiceStatusInterface::CLOSE:
                return $this->translator->trans('service.status.close.text');
            case ServiceStatusInterface::VACANCY:
                return $this->translator->trans('service.status.vacancy.text');
            default:
                return $this->translator->trans('service.status.open.text');
        }
    }
}
