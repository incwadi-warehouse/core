<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/genre')]
class GenreController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Genre::class)->findDemanded(
                $this->getUser()->getBranch(),
            ),
        );
    }

    /**
     * @Security("is_granted('ROLE_USER') and genre.getBranch() === user.getBranch() or is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Genre $genre): JsonResponse
    {
        return $this->json($genre);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/new', methods: ['POST'])]
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
            'msg' => 'Please enter a valid genre!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and genre.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
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
     * @Security("is_granted('ROLE_ADMIN') and genre.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Genre $genre): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($genre);
        $em->flush();

        return $this->json([
            'msg' => 'The genre was deleted successfully.',
        ]);
    }
}
