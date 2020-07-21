<?php

/*
 * This script is part of incwadi/core
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
 * @Route("/api/v1/branch")
 */
class BranchController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->isGranted('ROLE_ADMIN') ?
            $this->getDoctrine()->getRepository(Branch::class)->findAll() :
            $this->getDoctrine()->getRepository(Branch::class)->find(
                $this->getUser()->getBranch()->getId()
            ),
        );
    }

    /**
     * @Route("/my", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function my(): JsonResponse
    {
        return $this->json(
            $this->getUser()->getBranch()
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Branch $branch): JsonResponse
    {
        return $this->json($branch);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
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
            'msg' => 'Please enter a valid branch!',
        ]);
    }
}
