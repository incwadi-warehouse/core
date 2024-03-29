<?php

namespace App\Controller\Public;

use App\Entity\Branch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/public/branch')]
class BranchController extends AbstractController
{
    #[Route(path: '/', methods: ['GET'])]
    public function branch(ManagerRegistry $manager): JsonResponse
    {
        $branches = $manager
                ->getRepository(Branch::class)
                ->findByPublic(true);
        $processed = [];
        foreach ($branches as $branch) {
            $processed[] = [
                'id' => $branch->getId(),
                'name' => $branch->getName(),
                'content' => $branch->getContent()
            ];
        }

        return $this->json([
            'branches' => $processed,
        ]);
    }

    #[Route(path: '/show/{id}', methods: ['GET'])]
    public function show(Branch $branch): JsonResponse
    {
        if ($branch->getPublic() === false) {
            throw $this->createNotFoundException();
        }

        return $this->json([
            'id' => $branch->getId(),
            'name' => $branch->getName(),
            'content' => $branch->getContent()
        ]);
    }
}
