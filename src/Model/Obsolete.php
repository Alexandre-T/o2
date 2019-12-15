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

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

class Obsolete
{
    /**
     * Current date time.
     *
     * @var DateTimeImmutable
     */
    private static $now;

    /**
     * Obsolescence date.
     *
     * @var DateTimeInterface
     */
    private static $obsolescence;

    /**
     * Return the current date.
     *
     * @throws Exception this should not happened because I call constructor without argument.
     *
     * @return DateTimeInterface
     */
    public static function getCurrentDate(): DateTimeInterface
    {
        //I use a singleton to avoid to generate a lot of DateTimeInterface
        if (!self::$now instanceof DateTimeInterface) {
            self::$now = new DateTimeImmutable();
        }

        return self::$now;
    }

    /**
     * Return the obsolete date.
     *
     * @throws Exception This shall not happened because I call constructor without argument.
     *
     * @return DateTimeInterface
     */
    public static function getLimitedDate(): DateTimeInterface
    {
        if (!self::$obsolescence instanceof DateTimeInterface) {
            $now = self::getCurrentDate();
            $date = $now->format('Y-m').'-1 00:00:00.000';
            self::$obsolescence = new \DateTimeImmutable($date);
            self::$obsolescence->sub(new DateInterval('P1M'));
        }

        return self::$obsolescence;
    }

    /**
     * Return true if the argument is lesser than the obsolescence date.
     *
     * @param DateTimeInterface $date the date to compare
     *
     * @throws Exception this will never happen
     *
     * @return bool
     */
    public static function isObsolete(DateTimeInterface $date): bool
    {
        return self::getLimitedDate() > $date;
    }
}
