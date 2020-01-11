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

interface ProgrammationInterface
{
    /**
     * Constant for COST.
     */
    public const CREDIT_CAT = 5;

    public const CREDIT_EDC = 5;

    public const CREDIT_EGR = 5;

    public const CREDIT_ETHANOL = 10;

    public const CREDIT_FAP = 5;

    public const CREDIT_GEAR = 10;

    public const CREDIT_STAGE_ONE = 10;

    /**
     * Constant for GEAR field.
     */
    public const GEAR_AUTOMATIC = true;

    public const GEAR_MANUAL = false;

    public const GEARS = [self::GEAR_MANUAL, self::GEAR_AUTOMATIC];

    /**
     * Constant for ODB field.
     */
    public const ODB_BOOT = 1;

    public const ODB_ODB = 2;

    public const ODBS = [self::ODB_BOOT, self::ODB_ODB];

    /**
     * Constant for promotion.
     *
     * Promotion are subtracted to total.
     */
    public const PROMOTION_EGR_FAP = 5;

    public const PROMOTION_STAGE_ONE_ETHANOL = 5;

    /**
     * Constant for READ field.
     */
    public const READ_REAL = 1;

    public const READ_VIRTUAL = 2;

    public const READS = [self::READ_REAL, self::READ_VIRTUAL];

    /**
     * Is Cat asked?
     *
     * @return bool
     */
    public function isCatOff(): ?bool;

    /**
     * Is Edc15 asked?
     *
     * @return bool
     */
    public function isEdcOff(): ?bool;

    /**
     * Is Egr off asked?
     *
     * @return bool
     */
    public function isEgrOff(): ?bool;

    /**
     * Is Ethanol compatibility asked?
     *
     * @return bool
     */
    public function isEthanol(): ?bool;

    /**
     * Is FAP Off asked?
     *
     * @return bool
     */
    public function isFapOff(): ?bool;

    /**
     * Is gear asked?
     *
     * @return bool
     */
    public function isGear(): ?bool;

    /**
     * Is Stage1 reprogrammation asked?
     *
     * @return bool
     */
    public function isStageOne(): ?bool;
}
