<?php

namespace App\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StaffSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $staff = $args->getObject();

        if (!$staff instanceof Staff) {
            return;
        }

        $staff->setBranch(
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
