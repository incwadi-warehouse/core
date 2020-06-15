<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Staff;
use Incwadi\Core\Form\StaffType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/staff", name="staff_")
 */
class StaffController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Staff::class)->findBy(
                [
                    'branch' => $this->getUser()->getBranch()
                ]
            )
        );
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(Staff $staff): JsonResponse
    {
        return $this->json($staff);
    }

    /**
     * @Route("/new", methods={"POST"}, name="new")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
    {
        $staff = new Staff();
        $staff->setBranch(
            $this->getUser()->getBranch()
        );
        $form = $this->createForm(StaffType::class, $staff);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($staff);
            $em->flush();

            return $this->json($staff);
        }

        return $this->json([
            'msg' => 'Please enter a valid staff member!'
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Staff $staff): JsonResponse
    {
        $form = $this->createForm(StaffType::class, $staff);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($staff);
        }

        return $this->json([
            'msg' => 'Please enter a valid staff member!'
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Staff $staff): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($staff);
        $em->flush();

        return $this->json([
            'msg' => 'The staff member was successfully deleted.'
        ]);
    }
}
