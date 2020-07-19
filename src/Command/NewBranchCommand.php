<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Branch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NewBranchCommand extends Command
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
            ->setName('branch:new')
            ->setDescription('Creates a new branch.')
            ->setHelp('This command creates a new branch.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the branch')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        if (null === $input->getArgument('name')) {
            $input->setArgument(
                'name',
                $io->ask('What\'s the name of the new branch?')
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');

        $branch = new Branch();
        $branch->setName($name);

        $this->em->persist($branch);
        $this->em->flush();

        $io->success('Branch "'.$branch->getName().'" with id '.$branch->getId().' successfully created!');

        return Command::SUCCESS;
    }
}
