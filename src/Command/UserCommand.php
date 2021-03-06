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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * UserCommand class.
 *
 * Create a user.
 */
class UserCommand extends Command
{
    protected static $defaultName = 'app:user';

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * UserCommand constructor.
     *
     * @param EntityManagerInterface $manager Object manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    /**
     * Configure command.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Create a new user')
            ->addArgument('label', InputArgument::REQUIRED, 'Nom de l’utilisateur')
            ->addArgument('mail', InputArgument::REQUIRED, 'Email de l’utilisateur')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe de l’utilisateur')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Crée un administrateur')
        ;
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input  Input interface to handle data
     * @param OutputInterface $output Output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inOut = new SymfonyStyle($input, $output);
        $inOut->note('Process launched...');
        $mail = $input->getArgument('mail');
        $label = $input->getArgument('label');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setMail($mail);
        $user
            ->setGivenName('John')
            ->setName($label)
        ;
        $user
            ->setStreetAddress('.')
            ->setPostalCode('33680')
            ->setCountry('FR')
            ->setLocality('Lacanau')
        ;
        $user
            ->setType(PersonInterface::PHYSIC)
        ;

        if (!empty($password)) {
            $user->setPlainPassword($password);
        }

        $message = 'User created.';
        if (!empty($input->getOption('admin'))) {
            $message = 'Admin user created.';
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->manager->persist($user);
        $this->manager->flush();

        $inOut->success($message);

        return 1;
    }
}
