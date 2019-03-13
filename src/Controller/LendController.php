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
use Baldeweg\Entity\Lend;
use Baldeweg\Form\GenreType;
use Baldeweg\Form\LendType;
use Baldeweg\Form\CustomerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/lend", name="lend_")
 */
class LendController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Lend::class)->findAll()
        );
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(Request $request, Lend $lend): JsonResponse
    {
        return $this->json($lend);
    }

    /**
     * @Route("/new", methods={"POST"}, name="new")
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $lend = new Lend();
        $form = $this->createForm(LendType::class, $lend);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lend);
            $em->flush();

            return $this->json($lend);
        }

        return $this->json([
            'msg' => 'Please enter a valid lending!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_USER')")
     */
    public function delete(Lend $lend): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($lend);
        $em->flush();

        return $this->json([
            'msg' => 'The lending was successfully deleted.'
        ]);
    }
}
