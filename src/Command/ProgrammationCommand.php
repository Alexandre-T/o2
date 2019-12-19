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

use App\Entity\Programmation;
use App\Repository\ProgrammationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Programmation Command class.
 *
 * Drop old files.
 */
class ProgrammationCommand extends Command
{
    protected static $defaultName = 'app:programmation';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ProgrammationRepository|ObjectRepository
     */
    private $programmationRepository;

    /**
     * UserCommand constructor.
     *
     * @param EntityManagerInterface $entityManager Object manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->programmationRepository = $entityManager->getRepository(Programmation::class);
    }

    /**
     * Configure command.
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Drop obsolete programmation files')
        ;
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input  Input interface to handle data
     * @param OutputInterface $output Output interface
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $droppedOriginalFiles = $droppedFinalFiles = 0;
        $inOut = new SymfonyStyle($input, $output);
        $inOut->note('Process launched...');

        foreach ($this->programmationRepository->findObsolete() as $programmation) {
            /** @var Programmation $programmation */
            if (null !== $programmation->getOriginalFile()) {
                $file = $programmation->getOriginalFile();
                $this->entityManager->remove($file);
                $programmation->setOriginalFile(null);
                ++$droppedOriginalFiles;
            }

            if (null !== $programmation->getFinalFile()) {
                $file = $programmation->getFinalFile();
                $this->entityManager->remove($file);
                $programmation->setFinalFile(null);
                ++$droppedFinalFiles;
            }

            if (0 < ($droppedOriginalFiles + $droppedFinalFiles)) {
                $this->entityManager->flush();
            }

            $inOut->success(sprintf(
                '%d original files dropped, %d final files dropped.',
                $droppedOriginalFiles,
                $droppedFinalFiles
            ));
        }
    }
}
