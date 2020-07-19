<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Condition;
use Incwadi\Core\Form\ConditionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/condition", name="condition_")
 */
class ConditionController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Condition::class)->findByBranch($this->getUser()->getBranch())
        );
    }

    /**
     * @Route("/new", methods={"POST"}, name="new")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
    {
        $condition = new Condition();
        $condition->setBranch($this->getUser()->getBranch());
        $form = $this->createForm(ConditionType::class, $condition);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($condition);
            $em->flush();

            return $this->json($condition);
        }

        return $this->json([
            'msg' => 'Please enter a valid condition!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="show")
     * @Security("is_granted('ROLE_USER') and user.getBranch() === condition.getBranch()")
     */
    public function show(Condition $condition): JsonResponse
    {
        return $this->json($condition);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="edit")
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === condition.getBranch()")
     */
    public function edit(Request $request, Condition $condition): JsonResponse
    {
        $form = $this->createForm(ConditionType::class, $condition);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($condition);
        }

        return $this->json([
            'msg' => 'Please enter a valid condition!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete")
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === condition.getBranch()")
     */
    public function delete(Condition $condition): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findByCond($condition);
        foreach ($books as $book) {
            $book->setCond(null);
        }
        $em->remove($condition);
        $em->flush();

        return $this->json([
            'msg' => 'The condition was successfully deleted.',
        ]);
    }
}
