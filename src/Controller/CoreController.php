<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class CoreController extends AbstractController
{
    /**
     * @Route("/me", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function me(): JsonResponse
    {
        return $this->json([
            'id' => $this->getUser()->getId(),
            'username' => $this->getUser()->getUsername(),
            'roles' => $this->getUser()->getRoles(),
            'branch' => $this->getUser()->getBranch(),
            'isUser' => $this->isGranted('ROLE_USER'),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }
}
