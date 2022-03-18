<?php

namespace App\EventSubscriber;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AuthorSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $author = $args->getObject();

        if (!$author instanceof Author) {
            return;
        }

        $books = $this->em->getRepository(Book::class)->findByAuthor($author);
        foreach ($books as $book) {
            $book->setAuthor(null);
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }
}
