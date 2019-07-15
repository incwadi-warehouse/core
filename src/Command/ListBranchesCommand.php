<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Branch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListBranchesCommand extends Command
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->setName('branch:list')
            ->setDescription('Find and show all branches')
            ->setHelp('This command finds and shows all branches.')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $branches = $this->em->getRepository(Branch::class)->findAll();
        $data = [];
        foreach ($branches as $branch) {
            $data[] = [
                $branch->getId(),
                $branch->getName()
            ];
        }

        $io->table(
            ['Id', 'Name'],
            $data
        );

        return null;
    }
}
