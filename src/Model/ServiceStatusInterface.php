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

namespace App\Model;

/**
 * Service Status Interface.
 */
interface ServiceStatusInterface
{
    /**
     * The service is close.
     */
    public const CLOSE = 1;

    /**
     * The service is open.
     */
    public const OPEN = 0;

    /**
     * The service is close for a long time.
     */
    public const VACANCY = 2;
}
