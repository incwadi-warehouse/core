<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Controller;

use Baldeweg\Entity\Book;
use Baldeweg\Form\BookType;
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
                'term' => $request->query->get('term'),
                'stocked' => $request->query->has('stocked') ? $request->query->get('stocked') : true,
                'branch' => $request->query->has('branch') ? explode(',', $request->query->get('branch')) : [$this->getUser()->getBranch()]
            ],
            ($request->query->has('limit') ? $request->query->get('limit') : 20),
            ($request->query->has('offset') ? $request->query->get('offset') : 0)
        );

        return $this->json([
            'counter' => count($books),
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
        $book->setAdded(new \DateTime());
        $form = $this->createForm(BookType::class, $book);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
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
