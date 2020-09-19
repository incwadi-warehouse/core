<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\SavedSearch;
use Incwadi\Core\Form\SavedSearchType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/savedsearch")
 */
class SavedSearchController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(SavedSearch::class)->findByBranch($this->getUser()->getBranch())
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(SavedSearch $savedsearch): JsonResponse
    {
        return $this->json($savedsearch);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $savedsearch = new SavedSearch();
        $savedsearch->setBranch(
            $this->getUser()->getBranch()
        );

        $content = json_decode(
            $request->getContent(),
            true
        );
        $savedsearch->setName($content['name']);
        $savedsearch->setQuery($content['query']);

        if (0 === count($validator->validate($savedsearch))) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($savedsearch);
            $em->flush();

            return $this->json($savedsearch);
        }

        return $this->json([
            'msg' => 'Please enter a valid saved search!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, SavedSearch $savedsearch, ValidatorInterface $validator): JsonResponse
    {
        $content = json_decode(
            $request->getContent(),
            true
        );
        $savedsearch->setName($content['name']);
        $savedsearch->setQuery($content['query']);

        if (0 === count($validator->validate($savedsearch))) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($savedsearch);
        }

        return $this->json([
            'msg' => 'Please enter a valid saved search!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(SavedSearch $savedsearch): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($savedsearch);
        $em->flush();

        return $this->json([
            'msg' => 'The saved search was deleted successfully.',
        ]);
    }
}
