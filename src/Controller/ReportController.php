<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Report;
use Incwadi\Core\Form\ReportType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/report")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Report::class)->findBy(
                [
                    'branch' => $this->getUser()->getBranch(),
                ]
            )
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(Report $report): JsonResponse
    {
        return $this->json($report);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): JsonResponse
    {
        $report = new Report();
        $report->setBranch(
            $this->getUser()->getBranch()
        );
        $form = $this->createForm(ReportType::class, $report);
        $form->submit(
            json_decode(
                $request->getContent(),
                true
                )
            );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($report);
            $em->flush();

            return $this->json($report);
        }

        return $this->json([
            'msg' => 'Please enter a valid report!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Report $report): JsonResponse
    {
        $form = $this->createForm(ReportType::class, $report);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($report);
        }

        return $this->json([
            'msg' => 'Please enter a valid report!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Report $report): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($report);
        $em->flush();

        return $this->json([
            'msg' => 'The report was deleted successfully.',
        ]);
    }
}
