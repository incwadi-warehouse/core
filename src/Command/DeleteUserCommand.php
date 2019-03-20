<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Command;

use Doctrine\ORM\EntityManagerInterface;
use Baldeweg\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteUserCommand extends Command
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
            ->setName('user:delete')
            ->setDescription('Deletes a user')
            ->setHelp('This command deletes a user.')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->remove(
            $this->em->getRepository(User::class)->find(
                $input->getArgument('id')
            )
        );
        $this->em->flush();

        $io->success('The user was deleted!');

        return null;
    }
}
