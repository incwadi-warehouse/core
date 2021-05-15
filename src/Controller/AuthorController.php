<?php

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Author;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Form\AuthorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/author")
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/find", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function find(Request $request): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Author::class)->findDemanded($request->get('term'))
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(Author $author): JsonResponse
    {
        return $this->json($author);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->json($author);
        }

        return $this->json([
            'msg' => 'Please enter a valid author!',
        ]);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function edit(Request $request, Author $author): JsonResponse
    {
        $form = $this->createForm(AuthorType::class, $author);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($author);
        }

        return $this->json([
            'msg' => 'Please enter a valid author!',
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Author $author): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findBy(
            [
                'author' => $author,
            ]
        );
        foreach ($books as $book) {
            $book->setAuthor(null);
        }
        $em->remove($author);
        $em->flush();

        return $this->json([
            'msg' => 'The author was deleted successfully.',
        ]);
    }
}
