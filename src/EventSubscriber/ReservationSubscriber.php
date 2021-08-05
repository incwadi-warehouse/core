<?php

namespace App\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Reservation;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReservationSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $reservation = $args->getObject();

        if (!$reservation instanceof Reservation) {
            return;
        }

        $reservation->setBranch(
            $this->token->getToken()->getUser()->getBranch()
        );

        foreach ($reservation->getBooks() as $book) {
            $book->setReserved(true);
            $book->setReservedAt(new \DateTime());
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $reservation = $args->getObject();

        if (!$reservation instanceof Reservation) {
            return;
        }

        foreach ($reservation->getBooks() as $book) {
            $book->setReservation(null);
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
