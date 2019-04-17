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

namespace App\Entity;

interface PersonInterface
{
    /**
     * This is a moral person.
     */
    public const MORAL = false;

    /**
     * This is a physic person.
     */
    public const PHYSIC = true;

    /**
     * Available types.
     */
    public const TYPES = [self::MORAL, self::PHYSIC];
}
