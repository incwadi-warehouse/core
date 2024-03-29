<?php

namespace App\Controller\Public;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/public/reservation')]
class ReservationController extends AbstractController
{
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

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setOpen(true);
            $em->persist($reservation);
            $em->flush();

            return $this->json(['msg' => 'SUCCESS']);
        }

        return $this->json([
            'msg' => 'ERROR',
        ], 400);
    }
}
