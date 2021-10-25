<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/reservation')]
class ReservationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/list', methods: ['GET'])]
    public function list(): JsonResponse
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
     * @Security("is_granted('ROLE_USER') and reservation.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Reservation $reservation): JsonResponse
    {
        return $this->json($reservation);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            return $this->json($reservation);
        }

        return $this->json([
            'msg' => 'Please enter a valid reservation!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === reservation.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
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
     * @Security("is_granted('ROLE_USER') and user.getBranch() === reservation.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Reservation $reservation): JsonResponse
    {
        foreach ($reservation->getBooks() as $book) {
            if ($book->getReserved()) {
                throw new \Exception('Cannot delete reservations, because not all books are sold or removed.');
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($reservation);
        $em->flush();

        return $this->json([
            'msg' => 'The reservation was successfully deleted.',
        ]);
    }
}
