<?php

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Format;
use Incwadi\Core\Form\FormatType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/format")
 */
class FormatController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Format::class)->findBy(
                ['branch'=>$this->getUser()->getBranch()],
                ['name'=>'ASC']
            ),
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and format.getBranch() === user.getBranch()")
     */
    public function show(Format $format): JsonResponse
    {
        return $this->json($format);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
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
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN') and format.getBranch() === user.getBranch()")
     */
    public function edit(Request $request, Format $format): JsonResponse
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
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') and format.getBranch() === user.getBranch()")
     */
    public function delete(Format $format): JsonResponse
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
