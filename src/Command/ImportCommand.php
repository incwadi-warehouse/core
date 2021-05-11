<?php

namespace Incwadi\Core\Command;

use Incwadi\Core\Util\Import;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected Import $import;

    public function __construct(Import $import)
    {
        parent::__construct();
        $this->import = $import;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!is_file($input->getArgument('file'))) {
            $io->error('The file you are about to import does not exist.');

            return Command::FAILURE;
        }

        $data = $this->import->import(
            file_get_contents(
                $input->getArgument('file')
            )
        );

        $io->success('The import was successful!');

        return Command::SUCCESS;
    }
}
