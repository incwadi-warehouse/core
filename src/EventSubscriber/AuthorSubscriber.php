<?php

namespace Incwadi\Core\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Incwadi\Core\Entity\Author;
use Incwadi\Core\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class AuthorSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
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
