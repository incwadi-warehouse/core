<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class NewUserCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder
    ) {
        parent::__construct();
        $this->em = $em;
        $this->encoder = $encoder;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
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
            $input->getArgument('role') ?: 'ROLE_USER',
        ]);

        $this->em->persist($user);
        $this->em->flush();

        $io->listing([
            'Username: '.$user->getUsername(),
            'Password: '.$pass,
        ]);

        return Command::SUCCESS;
    }
}
