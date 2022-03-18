<?php

namespace App\Command;

use App\Entity\Branch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NewBranchCommand extends Command
{
    protected static $defaultName = 'branch:new';

    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Creates a new branch.')
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
                $io->ask("What's the name of the new branch?")
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
