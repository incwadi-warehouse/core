<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListUserCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setName('user:list')
            ->setDescription('Find and show all users')
            ->setHelp('This command finds and shows all users.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->em->getRepository(User::class)->findAll();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                $user->getId(),
                $user->getUsername(),
                implode(', ', $user->getRoles()),
                ($user->getBranch() ? $user->getBranch()->getName() : null)
            ];
        }

        $io->table(
            ['Id', 'User', 'Roles', 'Branch'],
            $data
        );
    }
}
