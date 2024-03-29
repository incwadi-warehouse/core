<?php

namespace App\Controller;

use App\Entity\Inventory;
use App\Form\InventoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/inventory')]
class InventoryController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(ManagerRegistry $manager): JsonResponse
    {
        return $this->json(
            $manager
                ->getRepository(Inventory::class)
                ->findBy(
                    ['branch' => $this->getUser()->getBranch()],
                    ['startedAt' => 'desc']
                )
        );
    }

    /**
     * @Security("is_granted('ROLE_USER') and inventory.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Inventory $inventory): JsonResponse
    {
        return $this->json($inventory);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request, ManagerRegistry $manager): JsonResponse
    {
        $active = $manager
            ->getRepository(Inventory::class)
            ->findActive(
                $this->getUser()->getBranch()
            );

        $inventory = new Inventory();
        $form = $this->createForm(InventoryType::class, $inventory);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid() && !$active) {
            $em = $manager->getManager();
            $em->persist($inventory);
            $em->flush();

            return $this->json($inventory);
        }

        return $this->json([
            'msg' => 'Please enter a valid inventory!',
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and inventory.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Inventory $inventory, ManagerRegistry $manager): JsonResponse
    {
        $form = $this->createForm(InventoryType::class, $inventory);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();

            $em->flush();

            return $this->json($inventory);
        }

        return $this->json([
            'msg' => 'Please enter a valid inventory!',
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and inventory.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Inventory $inventory, ManagerRegistry $manager): JsonResponse
    {
        $em = $manager->getManager();
        $em->remove($inventory);
        $em->flush();

        return $this->json([
            'msg' => 'The inventory was deleted successfully.',
        ]);
    }
}
