<?php

namespace App\Command;

use App\Repository\BranchRepository;
use App\Entity\Branch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'branch:list')]
class ListBranchesCommand extends Command
{
    public function __construct(private readonly BranchRepository $branchRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Find and show all branches')
            ->setHelp('This command finds and shows all branches.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $branches = $this->branchRepository->findAll();
        $data = [];
        foreach ($branches as $branch) {
            $data[] = [
                $branch->getId(),
                $branch->getName(),
            ];
        }

        $io->table(
            ['Id', 'Name'],
            $data
        );

        return Command::SUCCESS;
    }
}
