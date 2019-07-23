<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Util\Export;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends Command
{
    protected $export;
    private $em;


    public function __construct(EntityManagerInterface $em, Export $export)
    {
        $this->em = $em;
        $this->export = $export;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('incwadi:export')
            ->setDescription('Exports the data of the database to a file.')
            ->setHelp('Exports data from database to file')
            ->addArgument('file', InputArgument::REQUIRED, 'Where should the export file being stored?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if (is_file($input->getArgument('file'))) {
            $io->error('Export not possible, because the selected file already exists. Please choose a different name.');
            return;
        }

        $books = $this->em->getRepository(Book::class)->findAll();

        file_put_contents(
            $input->getArgument('file'),
            $this->export->export($books)
        );

        $io->success('The export was successful!');
    }
}
