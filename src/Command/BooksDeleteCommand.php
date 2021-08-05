<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Book;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BooksDeleteCommand extends Command
{
    protected static $defaultName = 'books:delete';

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Deletes the books from the database.')
            ->setHelp('Deletes books')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->em->getRepository(Book::class)->deleteBooks();

        $io->success('Cleaned up successfully!');

        return Command::SUCCESS;
    }
}
