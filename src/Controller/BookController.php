<?php

namespace Baldeweg\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Baldeweg\Entity\Book;
use Baldeweg\Form\BookType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/book", name="book_")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/new", methods={"POST"}, name="new")
     */
    public function new(Request $request): JsonResponse
    {
        $book = new Book();
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
}
