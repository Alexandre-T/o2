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
}
