<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Service\CoverShow;
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
     * @Route("/recommendation", methods={"GET"})
     */
    public function recommendation(CoverSHow $cover): JsonResponse
    {
        $books = $this
            ->getDoctrine()
            ->getRepository(Book::class)
            ->findBy([
                'sold' => false,
                'removed' => false,
                'lendOn' => null,
                'reserved' => false,
                'recommendation' => true,
            ]);

        $processed = [];
        foreach ($books as $book) {
            $processed[] = array_merge(
                [
                    'id' => $book->getId(),
                    'currency' => $book->getBranch()->getCurrency(),
                    'title' => $book->getTitle(),
                    'shortDescription' => $book->getShortDescription(),
                    'authorFirstname' => $book->getAuthor()->getFirstname(),
                    'authorSurname' => $book->getAuthor()->getSurname(),
                    'genre' => $book->getGenre()->getName(),
                    'price' => $book->getPrice(),
                    'releaseYear' => $book->getReleaseYear(),
                    'type' => $book->getType(),
                    'branchName' => $book->getBranch()->getName(),
                    'branchOrdering' => $book->getBranch()->getOrdering(),
                    'cond' => $book->getCond() ? $book->getCond()->getName() : null,
                ],
                $cover->show($book)
            );
        }

        return $this->json([
            'books' => $processed,
            'counter' => count($processed),
        ]);
    }
}
