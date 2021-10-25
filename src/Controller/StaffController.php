<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Form\StaffType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/staff')]
class StaffController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this
                ->getDoctrine()
                ->getRepository(Staff::class)
                ->findByBranch(
                    $this->getUser()->getBranch()
                )
        );
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Staff $staff): JsonResponse
    {
        return $this->json($staff);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $staff = new Staff();
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
            'msg' => 'Please enter a valid staff member!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
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
            'msg' => 'Please enter a valid staff member!',
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Staff $staff): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($staff);
        $em->flush();

        return $this->json([
            'msg' => 'The staff member was deleted successfully.',
        ]);
    }
}
