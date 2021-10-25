<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Form\BranchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/branch')]
class BranchController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
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
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Branch $branch): JsonResponse
    {
        return $this->json($branch);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === branch")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
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
