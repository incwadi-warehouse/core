<?php

namespace App\Controller;

use App\Entity\Branch;
use App\Form\BranchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/branch')]
class BranchController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(ManagerRegistry $manager): JsonResponse
    {
        return $this->json(
            $this->isGranted('ROLE_ADMIN') ?
            $manager->getRepository(Branch::class)->findAll() :
            $manager->getRepository(Branch::class)->find(
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
    public function edit(Request $request, Branch $branch, ManagerRegistry $manager): JsonResponse
    {
        $form = $this->createForm(BranchType::class, $branch);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->flush();

            return $this->json($branch);
        }

        return $this->json([
            'msg' => 'Please enter a valid branch!',
        ]);
    }
}
