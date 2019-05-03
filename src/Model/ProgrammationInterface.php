<?php

namespace App\Model;

interface ProgrammationInterface
{
    /**
     * Constant for ODB field.
     */
    public const ODB_BOOT = 1;
    public const ODB_ODB = 2;

    /**
     * Constant for GEAR field.
     */
    public const GEAR_AUTOMATIC = true;
    public const GEAR_MANUAL = false;

    /**
     * Constant for READ field.
     */
    public const READ_REAL = 1;
    public const READ_VIRTUAL = 2;

    /**
     * Constant for COST
     */
    const CREDIT_EDC = 5;
    const CREDIT_EGR = 5;
    const CREDIT_ETHANOL = 10;
    const CREDIT_FAP = 5;
    const CREDIT_STAGE_ONE = 10;

    /**
     * Constant for promotion.
     *
     * Promotion are subtracted to total.
     */
    const PROMOTION_EGR_FAP = 5;
    const PROMOTION_STAGE_ONE_ETHANOL = 10;
}
