<?php

namespace Incwadi\Core\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Incwadi\Core\Entity\Reservation;
use Incwadi\Core\Entity\Book;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class ReservationSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token, private EntityManagerInterface $em)
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
