<?php

namespace App\Model;

use DateInterval;
use DateTime;
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
     * @return DateTimeInterface
     *
     * @throws Exception this should not happened because I call constructor without argument.
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
     * @return DateTimeInterface
     *
     * @throws Exception This shall not happened because I call constructor without argument.
     */
    public static function getLimitedDate(): DateTimeInterface
    {
        if (!self::$obsolescence instanceof DateTimeInterface) {
            $now = self::getCurrentDate();
            $date = $now->format('Y-m') . '-1 00:00:00.000';
            self::$obsolescence = new DateTime($date);
            self::$obsolescence->sub(new DateInterval('P1M'));
        }

        return self::$obsolescence;
    }

    /**
     * Return true if the argument is lesser than the obsolescence date.
     *
     * @param DateTimeInterface $date the date to compare
     *
     * @return bool
     *
     * @throws Exception this will never happen
     */
    public static function isObsolete(DateTimeInterface $date): bool
    {
        return self::getLimitedDate() > $date;
    }
}