<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Reservation;
use Incwadi\Core\Form\ReservationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/list", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function list(Request $request): JsonResponse
    {
        return $this->json(
            $this
                ->getDoctrine()
                ->getRepository(Reservation::class)
                ->findByBranch(
                    $this->getUser()->getBranch(),
                    ['createdAt' => 'DESC']
                )
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and reservation.getBranch() === user.getBranch()")
     */
    public function show(Reservation $reservation): JsonResponse
    {
        return $this->json($reservation);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $reservation = new Reservation();
        $reservation->setBranch(
            $this->getUser()->getBranch()
        );
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($reservation->getBooks() as $book) {
                $book->setReserved(true);
                $book->setReservedAt(new \DateTime());
            }
            $em->persist($reservation);
            $em->flush();

            return $this->json($reservation);
        }

        return $this->json([
            'msg' => 'Please enter a valid reservation!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_USER') and user.getBranch() === reservation.getBranch()")
     */
    public function edit(Request $request, Reservation $reservation): JsonResponse
    {
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($reservation);
        }

        return $this->json([
            'msg' => 'Please enter a valid reservation!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === reservation.getBranch()")
     */
    public function delete(Reservation $reservation): JsonResponse
    {
        foreach ($reservation->getBooks() as $book) {
            if ($book->getReserved()) {
                throw new \Exception('Cannot delete reservations, because not all books are sold or removed.');
            }
        }

        foreach ($reservation->getBooks() as $book) {
            $book->setReservation(null);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($reservation);
        $em->flush();

        return $this->json([
            'msg' => 'The reservation was successfully deleted.',
        ]);
    }
}
