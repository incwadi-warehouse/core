<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/author')]
class AuthorController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/find', methods: ['GET'])]
    public function find(Request $request, ManagerRegistry $manager): JsonResponse
    {
        return $this->json(
            $manager->getRepository(Author::class)->findDemanded($request->get('term'))
        );
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Author $author): JsonResponse
    {
        return $this->json($author);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request, ManagerRegistry $manager): JsonResponse
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
            $em = $manager->getManager();
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
    #[Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Author $author, ManagerRegistry $manager): JsonResponse
    {
        $form = $this->createForm(AuthorType::class, $author);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
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
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Author $author, ManagerRegistry $manager): JsonResponse
    {
        $em = $manager->getManager();
        $em->remove($author);
        $em->flush();

        return $this->json([
            'msg' => 'The author was deleted successfully.',
        ]);
    }
}
