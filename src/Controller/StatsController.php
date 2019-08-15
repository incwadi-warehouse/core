<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/stats", name="stats_")
 */
class StatsController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="stats")
     * @Security("is_granted('ROLE_USER')")
     */
    public function stats(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Book::class);

        $all = count($repo->findAll());
        $available = count($repo->findBy([
            'sold' => false,
            'removed' => false
        ]));
        $sold = count($repo->findBySold(true));
        $removed = count($repo->findByRemoved(true));

        return $this->json([
            'all' => $all,
            'available' => $available,
            'sold' => $sold,
            'removed' => $removed
        ]);
    }
}
