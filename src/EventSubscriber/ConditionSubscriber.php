<?php

namespace Incwadi\Core\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Incwadi\Core\Entity\Condition;
use Incwadi\Core\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class ConditionSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token, private EntityManagerInterface $em)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $condition = $args->getObject();

        if (!$condition instanceof Condition) {
            return;
        }

        $condition->setBranch(
            $this->token->getToken()->getUser()->getBranch()
        );
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $condition = $args->getObject();

        if (!$condition instanceof Condition) {
            return;
        }

        $books = $this->em->getRepository(Book::class)->findByCond($condition);
        foreach ($books as $book) {
            $book->setCond(null);
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
