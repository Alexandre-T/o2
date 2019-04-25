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

use App\Exception\SettingsException;
use App\Manager\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Settings twig extension.
 */
class SettingsExtension extends AbstractExtension
{
    /**
     * Setting manager to retrieve each setting.
     *
     * @var SettingsManager
     */
    private $settingsManager;

    /**
     * Settings extension constructor.
     *
     * @param SettingsManager $settingsManager provided by injection dependencies
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * Declare faSort as a function.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            'settings' => new TwigFunction(
                'settings',
                [$this, 'settingsFunction']
            ),
        ];
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'app_settings_extension';
    }

    /**
     * Settings function.
     *
     * @param string $code code of setting
     *
     * @throws SettingsException when code does not exist
     *
     * @return mixed|null
     */
    public function settingsFunction(string $code)
    {
        return $this->settingsManager->getValue($code);
    }
}
