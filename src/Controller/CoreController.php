<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CoreController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Route("/v1", methods={"GET"}, name="index2")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json([]);
    }

    /**
     * @Route("/v1/me", methods={"GET"}, name="me")
     * @Security("is_granted('ROLE_USER')")
     */
    public function me(): JsonResponse
    {
        return $this->json([
            'id' => $this->getUser()->getId(),
            'username' => $this->getUser()->getUsername(),
            'roles' => $this->getUser()->getRoles(),
            'branch' => $this->getUser()->getBranch()
        ]);
    }
}
