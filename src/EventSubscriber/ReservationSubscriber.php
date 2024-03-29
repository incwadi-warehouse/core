<?php

namespace App\EventSubscriber;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReservationSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $token)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $reservation = $args->getObject();

        if (!$reservation instanceof Reservation) {
            return;
        }

        $branch = $reservation->getBooks()[0]->getBranch();

        if ($this->token->getToken() === null) {
            $reservation->setBranch($branch);
        } else {
            $reservation->setBranch(
                $this->token->getToken()->getUser()->getBranch()
            );
        }

        foreach ($reservation->getBooks() as $book) {
            if ($book->getBranch() !== $branch) {
                throw new \Exception('Not the correct branch.');
            }

            if ($book->getSold() || $book->getRemoved() || $book->getReserved()) {
                throw new \Exception('Not available.');
            }
        }

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
