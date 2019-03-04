<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 */

namespace Baldeweg\Controller;

use Baldeweg\Entity\Genre;
use Baldeweg\Entity\Book;
use Baldeweg\Entity\Branch;
use Baldeweg\Form\GenreType;
use Baldeweg\Form\BranchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/branch", name="branch_")
 */
class BranchController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Branch::class)->findAll()
        );
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Request $request, Branch $branch): JsonResponse
    {
        return $this->json($branch);
    }

    /**
     * @Route("/new", methods={"POST"}, name="new")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
    {
        $branch = new Branch();
        $form = $this->createForm(BranchType::class, $branch);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($branch);
            $em->flush();

            return $this->json($branch);
        }

        return $this->json([
            'msg' => 'Please enter a valid branch!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Branch $branch): JsonResponse
    {
        $form = $this->createForm(BranchType::class, $branch);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($branch);
        }

        return $this->json([
            'msg' => 'Please enter a valid branch!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Branch $branch): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findBy(
            [
                'branch' => $branch
            ]
        );
        foreach ($books as $book) {
            $book->setBranch(null);
        }
        $em->remove($branch);
        $em->flush();

        return $this->json([
            'msg' => 'The branch was successfully deleted.'
        ]);
    }
}
