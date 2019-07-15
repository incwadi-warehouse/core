<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Command;

use Incwadi\Core\Util\Import;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected $import;


    public function __construct(Import $import)
    {
        $this->import = $import;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('incwadi:import')
            ->setDescription('Imports the data of a csv file.')
            ->setHelp('Imports data from file')
            ->addArgument('file', InputArgument::REQUIRED, 'Where is the import file stored?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $data = $this->import->import(
            file_get_contents(
                $input->getArgument('file')
            )
        );

        $io->success('The import was successful!');
    }
}
