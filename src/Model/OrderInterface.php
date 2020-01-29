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
 * Interface Order.
 */
interface OrderInterface
{
    /**
     * Constants for credits.
     */
    public const CREDITED_ALREADY = true;
    public const CREDITED_NOT_YET = false;

    /**
     * Constants for nature.
     */
    public const NATURE_CMD = 2;
    public const NATURE_CREDIT = 1;
    public const NATURE_OLSX = 4;

    /**
     * Constants for status.
     */
    public const STATUS_CANCELED = 0;
    public const STATUS_CARTED = 1;
    public const STATUS_PAID = 2;
    public const STATUS_PENDING = 3;
}
