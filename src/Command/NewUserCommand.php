<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Command;

use Baldeweg\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NewUserCommand extends Command
{
    private $em;

    private $encoder;


    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->em = $em;
        $this->encoder = $encoder;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:new')
            ->setDescription('Creates a new user.')
            ->setHelp('This command creates a new user.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the user')
            ->addArgument('role', InputArgument::OPTIONAL, 'The role of the user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');
        $pass = $input->getArgument('password') ?: bin2hex(random_bytes(6));

        $user = new User();
        $user->setUsername($name);
        $user->setPassword(
            $this->encoder->encodePassword($user, $pass)
        );
        $user->setRoles([
            $input->getArgument('role') ?: 'ROLE_USER'
        ]);

        $this->em->persist($user);
        $this->em->flush();

        $io->listing([
            'Username: ' . $user->getUsername(),
            'Password: ' . $pass
        ]);

        return null;
    }
}
