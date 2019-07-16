<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Form\BookType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/book", name="book_")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index()
    {
        return $this->json([]);
    }

    /**
     * @Route("/find", methods={"GET"}, name="find")
     * @Security("is_granted('ROLE_USER')")
     */
    public function find(Request $request): JsonResponse
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findDemanded(
            [
                'term' => $request->query->get('term', null),
                'stocked' => $request->query->get('stocked', true),
                'branch' => $request->query->get('branch', $this->getUser()->getBranch()->getId()),
                'added' => $request->query->get('added', null),
                'genre' => $request->query->get('genre', 'any'),
                'lending' => $request->query->get('lending', null),
                'releaseYear' => $request->query->get('releaseYear', null),
                'type' => $request->query->get('type', null)
            ],
            $request->query->get('orderBy', 'asc'),
            $request->query->get('limit', 20),
            $request->query->get('offset', 0)
        );

        $counter = $this->getDoctrine()->getRepository(Book::class)->findDemanded(
            [
                'term' => $request->query->get('term', null),
                'stocked' => $request->query->get('stocked', true),
                'branch' => $request->query->get('branch', $this->getUser()->getBranch()->getId()),
                'added' => $request->query->get('added', null),
                'genre' => $request->query->get('genre', 'any'),
                'lending' => $request->query->get('lending', null),
                'releaseYear' => $request->query->get('releaseYear', null),
                'type' => $request->query->get('type', null)
            ],
            $request->query->get('orderBy', 'asc'),
            99999,
            $request->query->get('offset', 0)
        );

        return $this->json([
            'counter' => count($counter),
            'books' => $books
        ]);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_USER') and book.getBranch() === user.getBranch() or is_granted('ROLE_ADMIN')")
     */
    public function show(Request $request, Book $book): JsonResponse
    {
        return $this->json($book);
    }

    /**
     * @Route("/new", methods={"POST"}, name="new")
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $book = new Book();
        $book->setBranch(
            $this->getUser()->getBranch()
        );
        $form = $this->createForm(BookType::class, $book);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        $existingBook = $this->getDoctrine()->getRepository(Book::class)->findBy(
            [
                'branch' => $book->getBranch(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'genre' => $book->getGenre(),
                'price' => $book->getPrice(),
                'stocked' => $book->getStocked(),
                'releaseYear' => $book->getReleaseYear(),
                'type' => $book->getType(),
                'premium' => $book->getPremium()
            ]
        );
        if ($existingBook !== []) {
            return $this->json([
            'msg' => 'Book not saved, because it exists already!'
            ], 409);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->json($book);
        }

        return $this->json([
            'msg' => 'Please enter a valid book!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="edit")
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    public function edit(Request $request, Book $book): JsonResponse
    {
        $form = $this->createForm(BookType::class, $book);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        $existingBook = $this->getDoctrine()->getRepository(Book::class)->findOneBy(
            [
                'branch' => $book->getBranch(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'genre' => $book->getGenre(),
                'price' => $book->getPrice(),
                'stocked' => $book->getStocked(),
                'releaseYear' => $book->getReleaseYear(),
                'type' => $book->getType(),
                'premium' => $book->getPremium()
            ]
        );
        if ($existingBook !== null) {
            if ($existingBook->getId() !== $book->getId()) {
                return $this->json([
                'msg' => 'Book not saved, because it exists already!'
                ], 409);
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($book);
        }

        return $this->json([
            'msg' => 'Please enter a valid book!'
        ]);
    }

    /**
     * @Route("/toggleStocking/{id}", methods={"PUT"}, name="toggleStocking")
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    public function toggleStocking(Book $book): JsonResponse
    {
        $book->setStocked(!$book->getStocked());
        $this->getDoctrine()->getManager()->flush();

        return $this->json($book);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === book.getBranch()")
     */
    public function delete(Book $book): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();

        return $this->json([
            'msg' => 'The book was successfully deleted.'
        ]);
    }
}
