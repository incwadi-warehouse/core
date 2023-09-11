<?php

namespace App\EventSubscriber;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BookSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private HttpClientInterface $client,
        private Security $security,
    ) {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $book = $args->getObject();
        $user = $this->security->getUser();

        if (!$book instanceof Book) {
            return;
        }

        $indexUid = 'products_' . $user->getBranch()->getId();

        $response = $this->client->request(
            'POST',
            '/indexes/'.$indexUid.'/documents',
            ["body" => []]
        );
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $book = $args->getObject();
        $user = $this->security->getUser();

        if (!$book instanceof Book) {
            return;
        }

        $indexUid = 'products_' . $user->getBranch()->getId();

        $response = $this->client->request(
            'PUT',
            '/indexes/'.$indexUid.'/documents',
            ["body" => []]
        );
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $book = $args->getObject();
        $user = $this->security->getUser();

        if (!$book instanceof Book) {
            return;
        }

        $indexUid = 'products_' . $user->getBranch()->getId();

        $document = $this->client->request(
            'POST',
            '/indexes/'.$indexUid.'/documents/fetch',
            ["body" => ["filter" => null]]
        );

        $response = $this->client->request(
            'DELETE',
            '/indexes/'.$indexUid.'/documents/'.$document[0]->id
        );
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }
}
