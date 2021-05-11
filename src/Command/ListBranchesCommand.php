<?php

namespace Incwadi\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Branch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListBranchesCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setName('branch:list')
            ->setDescription('Find and show all branches')
            ->setHelp('This command finds and shows all branches.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $branches = $this->em->getRepository(Branch::class)->findAll();
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
