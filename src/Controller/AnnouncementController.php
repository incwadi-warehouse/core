<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use Baldeweg\Bundle\ApiBundle\AbstractApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/announcement')]
class AnnouncementController extends AbstractApiController
{
    private $fields = ['id', 'title', 'body'];

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(ManagerRegistry $manager): JsonResponse
    {
        return $this->setResponse()->collection(
            $this->fields,
            $manager->getRepository(Announcement::class)->findAnnouncements()
        );
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/{announcement}', methods: ['GET'])]
    public function show(Announcement $announcement): JsonResponse
    {
        return $this->setResponse()->single($this->fields, $announcement);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request, ManagerRegistry $manager): JsonResponse
    {
        $announcement = new Announcement();
        $form = $this->createForm(AnnouncementType::class, $announcement);

        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->persist($announcement);
            $em->flush();

            return $this->setResponse()->single($this->fields, $announcement);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/{announcement}', methods: ['PUT'])]
    public function edit(Announcement $announcement, Request $request, ManagerRegistry $manager): JsonResponse
    {
        $form = $this->createForm(AnnouncementType::class, $announcement);

        $form->submit(
            $this->submitForm($request)
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->flush();

            return $this->setResponse()->single($this->fields, $announcement);
        }

        return $this->setResponse()->invalid();
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[Route(path: '/{announcement}', methods: ['DELETE'])]
    public function delete(Announcement $announcement, ManagerRegistry $manager): JsonResponse
    {
        $em = $manager->getManager();
        $em->remove($announcement);
        $em->flush();

        return $this->setResponse()->deleted();
    }
}
