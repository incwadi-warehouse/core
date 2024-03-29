<?php

namespace App\EventSubscriber;

use App\Entity\Book;
use App\Entity\Condition;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ConditionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $token, private readonly EntityManagerInterface $em)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $condition = $args->getObject();

        if (!$condition instanceof Condition) {
            return;
        }

        if($this->token->getToken()){
            $condition->setBranch(
                $this->token->getToken()->getUser()->getBranch()
            );
        }
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
