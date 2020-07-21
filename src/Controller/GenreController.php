<?php

/*
 * This script is part of incwadi/core
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
 * @Route("/api/v1/genre")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Genre::class)->findByBranch(
                $this->getUser()->getBranch(),
                ['name' => 'ASC']
            ),
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and genre.getBranch() === user.getBranch() or is_granted('ROLE_ADMIN')")
     */
    public function show(Genre $genre): JsonResponse
    {
        return $this->json($genre);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
    {
        $genre = new Genre();
        $genre->setBranch($this->getUser()->getBranch());
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
            'msg' => 'Please enter a valid genre!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN') and genre.getBranch() === user.getBranch()")
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
            'msg' => 'Please enter a valid genre!',
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') and genre.getBranch() === user.getBranch()")
     */
    public function delete(Genre $genre): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findBy(
            [
                'genre' => $genre,
            ]
        );
        foreach ($books as $book) {
            $book->setGenre(null);
        }
        $em->remove($genre);
        $em->flush();

        return $this->json([
            'msg' => 'The genre was deleted successfully.',
        ]);
    }
}
