<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/reservation')]
class ReservationController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/list', methods: ['GET'])]
    public function list(ManagerRegistry $manager): JsonResponse
    {
        return $this->json(
            $manager
                ->getRepository(Reservation::class)
                ->findByBranch(
                    $this->getUser()->getBranch(),
                    ['createdAt' => 'DESC']
                )
        );
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/status', methods: ['GET'])]
    public function status(ManagerRegistry $manager): JsonResponse
    {
        return $this->json([
            'open' =>
                count($manager
                    ->getRepository(Reservation::class)
                    ->findBy(
                        [
                            'branch' => $this->getUser()->getBranch(),
                            'open' => true
                        ],
                        ['createdAt' => 'DESC']
                    )
                )
        ]);
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
    public function new(Request $request, ManagerRegistry $manager): JsonResponse
    {
        $em = $manager->getManager();

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        $reservation->setOpen(true);

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
    public function edit(Request $request, Reservation $reservation, ManagerRegistry $manager): JsonResponse
    {
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
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
    public function delete(Reservation $reservation, ManagerRegistry $manager): JsonResponse
    {
        foreach ($reservation->getBooks() as $book) {
            if ($book->getReserved()) {
                throw new \Exception('Cannot delete reservations, because not all books are sold or removed.');
            }
        }

        $em = $manager->getManager();
        $em->remove($reservation);
        $em->flush();

        return $this->json([
            'msg' => 'The reservation was successfully deleted.',
        ]);
    }
}
