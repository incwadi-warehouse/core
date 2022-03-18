<?php

namespace App\EventSubscriber;

use App\Entity\Book;
use App\Entity\Inventory;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class InventorySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $token, private readonly EntityManagerInterface $em)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $inventory = $args->getObject();

        if (!$inventory instanceof Inventory) {
            return;
        }

        $inventory->setBranch(
            $this->token->getToken()->getUser()->getBranch()
        );
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $inventory = $args->getObject();

        if (!$inventory instanceof Inventory) {
            return;
        }

        if (null === $inventory->getEndedAt()) {
            return;
        }

        $this->em->getRepository(Book::class)->removeNotFoundBooks(
            $this->token->getToken()->getUser()->getBranch()
        );

        $this->em->getRepository(Book::class)->resetInventory(
            $this->token->getToken()->getUser()->getBranch()
        );
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }
}
