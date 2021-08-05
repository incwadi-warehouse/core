<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Format;
use App\Form\FormatType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/format')]
class FormatController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index() : JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Format::class)->findBy(
                ['branch' => $this->getUser()->getBranch()],
                ['name' => 'ASC']
            ),
        );
    }

    /**
     * @Security("is_granted('ROLE_USER') and format.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Format $format) : JsonResponse
    {
        return $this->json($format);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request) : JsonResponse
    {
        $format = new Format();
        $format->setBranch($this->getUser()->getBranch());

        $form = $this->createForm(FormatType::class, $format);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($format);
            $em->flush();

            return $this->json($format);
        }

        return $this->json([
            'msg' => 'Please enter a valid format!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and format.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Format $format) : JsonResponse
    {
        $form = $this->createForm(FormatType::class, $format);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($format);
        }

        return $this->json([
            'msg' => 'Please enter a valid format!',
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and format.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Format $format) : JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findBy(
            [
                'format' => $format,
            ]
        );
        foreach ($books as $book) {
            $book->setFormat(null);
        }
        $em->remove($format);
        $em->flush();

        return $this->json([
            'msg' => 'The format was deleted successfully.',
        ]);
    }
}
