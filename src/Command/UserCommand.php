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

namespace App\Command;

use App\Entity\PersonInterface;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCommand extends Command
{
    protected static $defaultName = 'app:user';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * UserCommand constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct();
        $this->objectManager = $objectManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new user')
            ->addArgument('label', InputArgument::REQUIRED, 'Nom de l’utilisateur')
            ->addArgument('mail', InputArgument::REQUIRED, 'Email de l’utilisateur')
            ->addArgument('password', InputArgument::OPTIONAL, 'Email de l’utilisateur')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Crée un administrateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->note('Process launched...');
        $mail = $input->getArgument('mail');
        $label = $input->getArgument('label');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setMail($mail);
        $user
            ->setGivenName('John')
            ->setName($label)
            ->setStreetAddress('.')
            ->setPostalCode('33680')
            ->setCountry('FR')
            ->setLocality('Lacanau')
            ->setType(PersonInterface::PHYSIC);

        if (!empty($password)) {
            $user->setPlainPassword($password);
        }

        if (!empty($input->getOption('admin'))) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->objectManager->persist($user);
        $this->objectManager->flush();

        $io->success('User created.');
    }
}
