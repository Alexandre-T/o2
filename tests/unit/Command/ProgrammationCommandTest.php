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

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Programmation command test.
 *
 * @internal
 * @coversDefaultClass
 */
class ProgrammationCommandTest extends KernelTestCase
{
    /**
     * Test to execute command.
     */
    public function testAdminWithArguments(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:programmation');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            // pass arguments to the helper
            // NO ONE
            // prefix the key with two dashes when passing options,
            // NO ONE
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('4 original files dropped, 4 final files dropped.', $output);
    }
}
