<?php

namespace App\Controller\Public;

use App\Entity\Genre;
use App\Entity\Branch;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Baldeweg\Bundle\ApiBundle\AbstractApiController;

#[Route(path: '/api/public/genre')]
class GenreController extends AbstractApiController
{
    private $fields = ['id', 'name'];

    #[Route(path: '/{branch}', methods: ['GET'])]
    public function list(Branch $branch, ManagerRegistry $manager): JsonResponse
    {
        if (!$branch->getPublic()) {
            throw $this->createNotFoundException();
        }

        $genres = $manager->getRepository(Genre::class)->findDemanded($branch);

        if (!$genres) {
            throw $this->createNotFoundException();
        }

        return $this->setResponse()->collection($this->fields, $genres);
    }
}
