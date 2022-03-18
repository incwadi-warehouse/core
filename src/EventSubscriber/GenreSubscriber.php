<?php

namespace App\EventSubscriber;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GenreSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $token)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $genre = $args->getObject();

        if (!$genre instanceof Genre) {
            return;
        }

        if (null !== $genre->getBranch()) {
            return;
        }

        $genre->setBranch(
            $this->token->getToken()->getUser()->getBranch()
        );
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }
}
