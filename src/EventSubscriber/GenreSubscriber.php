<?php

namespace Incwadi\Core\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Incwadi\Core\Entity\Genre;
use Incwadi\Core\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class GenreSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token, private EntityManagerInterface $em)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $genre = $args->getObject();

        if (!$genre instanceof Genre) {
            return;
        }
        if(null !== $genre->getBranch()) {
            return;
        }

        $genre->setBranch(
            $this->token->getToken()->getUser()->getBranch()
        );
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $genre = $args->getObject();

        if (!$genre instanceof Genre) {
            return;
        }

        $books = $this->em->getRepository(Book::class)->findByGenre($genre);
        foreach ($books as $book) {
            $book->setGenre(null);
        }
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preRemove,
        ];
    }
}
