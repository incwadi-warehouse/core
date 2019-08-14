<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Lending;
use Incwadi\Core\Form\LendingType;
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
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Lending::class)->findAll()
        );
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_USER') and lending.getBook().getBranch() === user.getBranch()")
     */
    public function show(Lending $lending): JsonResponse
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
            'msg' => 'Please enter a valid lending!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_USER') and lending.getBook().getBranch() === user.getBranch()")
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
