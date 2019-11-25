<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Form\BranchType;
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
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            [
                'branches' => $this->isGranted('ROLE_ADMIN') ?
                    $this->getDoctrine()->getRepository(Branch::class)->findAll() :
                    [
                        $this->getDoctrine()->getRepository(Branch::class)->find(
                            $this->getUser()->getBranch()->getId()
                        )
                    ]
            ]
        );
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Branch $branch): JsonResponse
    {
        return $this->json($branch);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="edit")
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === branch")
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
}
