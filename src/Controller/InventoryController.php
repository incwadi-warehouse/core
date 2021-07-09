<?php

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Inventory;
use Incwadi\Core\Form\InventoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/inventory")
 */
class InventoryController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this
                ->getDoctrine()
                ->getRepository(Inventory::class)
                ->findBy(
                    ['branch' => $this->getUser()->getBranch()],
                    ['startedAt' => 'desc']
                )
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and inventory.getBranch() === user.getBranch()")
     */
    public function show(Inventory $inventory): JsonResponse
    {
        return $this->json($inventory);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
    {
        $active = $this
            ->getDoctrine()
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventory);
            $em->flush();

            return $this->json($inventory);
        }

        return $this->json([
            'msg' => 'Please enter a valid inventory!',
        ]);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_USER') and inventory.getBranch() === user.getBranch()")
     */
    public function edit(Request $request, Inventory $inventory): JsonResponse
    {
        $form = $this->createForm(InventoryType::class, $inventory);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();

            return $this->json($inventory);
        }

        return $this->json([
            'msg' => 'Please enter a valid inventory!',
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') and inventory.getBranch() === user.getBranch()")
     */
    public function delete(Inventory $inventory): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventory);
        $em->flush();

        return $this->json([
            'msg' => 'The inventory was deleted successfully.',
        ]);
    }
}
