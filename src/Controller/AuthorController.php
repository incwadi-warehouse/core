<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[\Symfony\Component\Routing\Annotation\Route(path: '/api/author')]
class AuthorController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/find', methods: ['GET'])]
    public function find(Request $request) : JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Author::class)->findDemanded($request->get('term'))
        );
    }
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['GET'])]
    public function show(Author $author) : JsonResponse
    {
        return $this->json($author);
    }
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/new', methods: ['POST'])]
    public function new(Request $request) : JsonResponse
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
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Author $author) : JsonResponse
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
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Author $author) : JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();

        return $this->json([
            'msg' => 'The author was deleted successfully.',
        ]);
    }
}
