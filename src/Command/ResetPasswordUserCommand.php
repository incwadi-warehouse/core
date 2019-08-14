<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
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

class ResetPasswordUserCommand extends Command
{
    private $em;
    private $encoder;


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
            ->setName('user:reset-password')
            ->setDescription('Resets the password of a user.')
            ->setHelp('This command resets the password of a user.')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $user = $this->em->getRepository(User::class)->find(
            $input->getArgument('id')
        );
        $pass = bin2hex(random_bytes(6));
        $user->setPassword(
            $this->encoder->encodePassword($user, $pass)
        );
        $this->em->flush();

        $io->success('Passwort: ' . $pass);
    }
}
