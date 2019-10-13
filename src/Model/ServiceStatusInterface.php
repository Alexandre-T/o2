<?php

namespace App\Model;

/**
 * Service Status Interface.
 */
interface ServiceStatusInterface
{
    /**
     * The service is open.
     */
    public const OPEN = 0;

    /**
     * The service is close.
     */
    public const CLOSE = 1;

    /**
     * The service is close for a long time.
     */
    public const VACANCY = 2;
}