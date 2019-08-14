<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Genre;
use Incwadi\Core\Form\GenreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/genre", name="genre_")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            [
                'genres' => $this->getDoctrine()->getRepository(Genre::class)->findAll()
            ]
        );
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(Genre $genre): JsonResponse
    {
        return $this->json($genre);
    }

    /**
     * @Route("/new", methods={"POST"}, name="new")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($genre);
            $em->flush();

            return $this->json($genre);
        }

        return $this->json([
            'msg' => 'Please enter a valid genre!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Genre $genre): JsonResponse
    {
        $form = $this->createForm(GenreType::class, $genre);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($genre);
        }

        return $this->json([
            'msg' => 'Please enter a valid genre!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Genre $genre): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findBy(
            [
                'genre' => $genre
            ]
        );
        foreach ($books as $book) {
            $book->setGenre(null);
        }
        $em->remove($genre);
        $em->flush();

        return $this->json([
            'msg' => 'The genre was successfully deleted.'
        ]);
    }
}
