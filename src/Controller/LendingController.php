<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Controller;

use Baldeweg\Entity\Book;
use Baldeweg\Entity\Genre;
use Baldeweg\Entity\Customer;
use Baldeweg\Entity\Lending;
use Baldeweg\Form\GenreType;
use Baldeweg\Form\LendingType;
use Baldeweg\Form\CustomerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/lending", name="lending_")
 */
class LendingController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Lending::class)->findAll()
        );
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(Request $request, Lending $lending): JsonResponse
    {
        return $this->json($lending);
    }

    /**
     * @Route("/new", methods={"POST"}, name="new")
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $lending = new Lending();
        $form = $this->createForm(LendingType::class, $lending);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lending);
            $em->flush();

            return $this->json($lending);
        }

        return $this->json([
            'msg' => 'Please enter a valid lendinging!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_USER')")
     */
    public function delete(Lending $lending): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($lending);
        $em->flush();

        return $this->json([
            'msg' => 'The lending was successfully deleted.'
        ]);
    }
}
