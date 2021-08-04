<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use App\Service\Portability\Export;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/*
 * Deprecated
 */
class ExportCommand extends Command
{
    private EntityManagerInterface $em;
    private Export $export;

    public function __construct(EntityManagerInterface $em, Export $export)
    {
        parent::__construct();
        $this->em = $em;
        $this->export = $export;
    }

    protected function configure(): void
    {
        $this
            ->setName('incwadi:export')
            ->setDescription('Exports the data of the database to a file.')
            ->setHelp('Exports data from database to file')
            ->addArgument('file', InputArgument::REQUIRED, 'Where should the export file be written?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (is_file($input->getArgument('file'))) {
            $io->error('Export not possible, because the selected file already exists. Please choose a different name.');

            return Command::FAILURE;
        }

        $books = $this->em->getRepository(Book::class)->findAll();

        file_put_contents(
            $input->getArgument('file'),
            $this->export->export($books)
        );

        $io->success('The export was successful!');

        return Command::SUCCESS;
    }
}
