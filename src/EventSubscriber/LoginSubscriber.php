<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onSecurityAuthenticationSuccess(AuthenticationEvent $event)
    {
        if (!$event->getAuthenticationToken() instanceof JWTUserToken) {
            return;
        }

        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLogin(new \DateTime());
        $this->em->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'security.authentication.success' => 'onSecurityAuthenticationSuccess',
        ];
    }
}
