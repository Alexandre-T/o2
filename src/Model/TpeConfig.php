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
/**
 * This file is part of the o2 Application.
 *
 * PHP version 7.2
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Model;

class TpeConfig
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * TpeConfig constructor.
     *
     * @param array $configuration configuration provided by injection
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
        $this->configuration['parameters']['debug'] = false;
    }

    /**
     * Configuration getter.
     *
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration['parameters'];
    }
}
