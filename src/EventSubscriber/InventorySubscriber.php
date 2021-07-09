<?php

namespace Incwadi\Core\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Incwadi\Core\Entity\Inventory;
use Incwadi\Core\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class InventorySubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token, private EntityManagerInterface $em)
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

        if(null === $inventory->getEndedAt()) {
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
            Events::preUpdate
        ];
    }
}
