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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NewBranchCommand extends Command
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
            ->setName('branch:new')
            ->setDescription('Creates a new branch.')
            ->setHelp('This command creates a new branch.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the branch')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');

        $branch = new Branch();
        $branch->setName($name);

        $this->em->persist($branch);
        $this->em->flush();

        $io->success('Branch "' . $branch->getName() . '" with id ' . $branch->getId() . ' successfully created!');

        return null;
    }
}
