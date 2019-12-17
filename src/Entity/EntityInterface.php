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

/**
 * Entity Interface.
 *
 * @category Entity
 */
interface EntityInterface
{
    /**
     * Return the id or null if entity was never saved.
     */
    public function getId(): ?int;

    /**
     * Return the label of entity.
     */
    public function getLabel(): string;
}
