<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/public/book")
 */
class BookPublicController extends AbstractController
{
    /**
     * @Route("/find", methods={"GET"})
     */
    public function find(Request $request): JsonResponse
    {
        return $this->json(
            $this
                ->getDoctrine()
                ->getRepository(Book::class)
                ->findDemanded(
                    json_decode(
                        $request->query->get('options'),
                        true
                    ),
                    true
                )
        );
    }

    /**
     * @Route("/branch", methods={"GET"})
     */
    public function branch(): JsonResponse
    {
        $branches = $this
                ->getDoctrine()
                ->getRepository(Branch::class)
                ->findAll();
        $processed = [];
        foreach ($branches as $branch) {
            $processed[] = [
                'id' => $branch->getId(),
                'name' => $branch->getName(),
            ];
        }

        return $this->json([
            'branches' => $processed,
        ]);
    }
}
