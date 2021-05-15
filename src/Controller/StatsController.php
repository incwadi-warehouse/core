<?php

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/stats")
 * Deprecated
 */
class StatsController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function stats(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Book::class);

        $all = count($repo->findAll());
        $available = count($repo->findBy([
            'sold' => false,
            'removed' => false,
            'reserved' => false,
        ]));
        $reserved = count($repo->findByReserved(true));
        $sold = count($repo->findBySold(true));
        $removed = count($repo->findByRemoved(true));

        return $this->json([
            'all' => $all,
            'available' => $available,
            'reserved' => $reserved,
            'sold' => $sold,
            'removed' => $removed,
        ]);
    }
}
