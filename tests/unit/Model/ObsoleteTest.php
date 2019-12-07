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

namespace App\Tests\unit\Model;

use App\Model\Obsolete;
use Codeception\Test\Unit;
use DateInterval;
use DateTime;
use Exception;

/**
 * @internal
 * @coversDefaultClass
 */
class ObsoleteTest extends Unit
{
    /**
     * Obsolete test.
     *
     * @throws Exception
     */
    public function testSomeDates(): void
    {
        $actual = new DateTime();
        self::assertFalse(Obsolete::isObsolete($actual));
        $actual->sub(new DateInterval('P2M'));
        self::assertTrue(Obsolete::isObsolete($actual));
    }

}