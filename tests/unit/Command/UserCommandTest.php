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

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * User command test.
 *
 * @internal
 * @coversDefaultClass
 */
class UserCommandTest extends KernelTestCase
{
    /**
     * Test to create an admin with all arguments.
     */
    public function testAdminWithArguments(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),

            // pass arguments to the helper
            'label' => 'TestAdmin',
            'mail' => 'test-admin@example.org',
            'password' => 'test-admin',

            // prefix the key with two dashes when passing options,
            '--admin' => true,
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Admin user created.', $output);
    }

    /**
     * Test the command with all arguments.
     */
    public function testWithArguments(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),

            // pass arguments to the helper
            'label' => 'TestUser',
            'mail' => 'test-user@example.org',
            'password' => 'test-user',

            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] User created.', $output);
    }

    /**
     * Test the command with one argument.
     */
    public function testWithOneArgument(): void
    {
        self::expectExceptionMessage('Not enough arguments (missing: "mail, password").');
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),

            // pass arguments to the helper
            'label' => 'TestUser',

            // prefix the key with two dashes when passing options,
            // e.g: '--some-option' => 'option_value',
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Not enough arguments (missing: "label, mail").', $output);
    }

    /**
     * Test the command without arguments.
     */
    public function testWithoutArguments(): void
    {
        self::expectExceptionMessage('Not enough arguments (missing: "label, mail, password").');
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
    }
}
