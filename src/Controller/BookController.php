<?php

/*
 * This script is part of incwadi/core
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
                'sold' => $request->query->get('sold', false),
                'removed' => $request->query->get('removed', false),
                'branch' => $request->query->get('branch', $this->getUser()->getBranch()->getId()),
                'added' => $request->query->get('added', null),
                'genre' => $request->query->get('genre', false),
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
                'sold' => $request->query->get('sold', false),
                'removed' => $request->query->get('removed', false),
                'branch' => $request->query->get('branch', $this->getUser()->getBranch()->getId()),
                'added' => $request->query->get('added', null),
                'genre' => $request->query->get('genre', false),
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
     * @Route("/clean", methods={"DELETE"}, name="clean")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function clean()
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Book::class)->deleteBooks(0);
        $em->flush();

        return $this->json(['msg' => 'Cleaned up successfully!']);
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
                'sold' => $book->getSold(),
                'releaseYear' => $book->getReleaseYear(),
                'type' => $book->getType()
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
                'sold' => $book->getSold(),
                'releaseYear' => $book->getReleaseYear(),
                'type' => $book->getType()
            ]
        );
        if (null !== $existingBook) {
            if ($existingBook->getId() !== $book->getId()) {
                return $this->json([
                'msg' => 'Book not saved, because it exists already!'
                ], 409);
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            // sold
            if (true === $book->getSold() && null === $book->getSoldOn()) {
                $book->setSoldOn(new \DateTime());
            }
            // revert sold
            if (false === $book->getSold() && null !== $book->getSoldOn()) {
                $book->setSoldOn(null);
            }
            // removed
            if (true === $book->getRemoved() && null === $book->getRemovedOn()) {
                $book->setRemovedOn(new \DateTime());
            }
            // revert removed
            if (false === $book->getRemoved() && null !== $book->getRemovedOn()) {
                $book->setRemovedOn(null);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($book);
        }

        return $this->json([
            'msg' => 'Please enter a valid book!'
        ]);
    }

    /**
     * @Route("/sell/{id}", methods={"PUT"}, name="sell")
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    public function sell(Book $book): JsonResponse
    {
        $book->setSold(!$book->getSold());
        $book->setSoldOn(null === $book->getSoldOn() ? new \DateTime() : null);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($book);
    }

    /**
     * @Route("/remove/{id}", methods={"PUT"}, name="remove")
     * @Security("is_granted('ROLE_USER') and user.getBranch() === book.getBranch()")
     */
    public function remove(Book $book): JsonResponse
    {
        $book->setRemoved(!$book->getRemoved());
        $book->setRemovedOn(null === $book->getRemovedOn() ? new \DateTime() : null);
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
