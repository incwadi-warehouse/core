<?php

namespace App\Controller;

use App\Entity\Bookmark;
use App\Form\BookmarkType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/bookmark')]
class BookmarkController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(ManagerRegistry $manager): JsonResponse
    {
        return $this->json(
            $manager
                ->getRepository(Bookmark::class)
                ->findBy([
                    'branch' => $this->getUser()->getBranch(),
                ])
        );
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === bookmark.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Bookmark $bookmark): JsonResponse
    {
        return $this->json($bookmark);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request, ManagerRegistry $manager): JsonResponse
    {
        $bookmark = new Bookmark();
        $bookmark->setBranch($this->getUser()->getBranch());

        $form = $this->createForm(BookmarkType::class, $bookmark);
        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        if ($form->isSubmitted() && $form->isValid()) {
            if (null == $bookmark->getName()) {
                $bookmark->setName(parse_url($bookmark->getUrl())['host']);
            }

            $em = $manager->getManager();
            $em->persist($bookmark);
            $em->flush();

            return $this->json($bookmark);
        }

        return $this->json([
            'error' => 'Please enter a bookmark url.',
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === bookmark.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Bookmark $bookmark, ManagerRegistry $manager): JsonResponse
    {
        $editForm = $this->createForm(BookmarkType::class, $bookmark);
        $editForm->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $manager->getManager();
            $em->flush();

            return $this->json($bookmark);
        }

        return $this->json([
            'msg' => 'Please enter a valid bookmark!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === bookmark.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Bookmark $bookmark, ManagerRegistry $manager): JsonResponse
    {
        $em = $manager->getManager();
        $em->remove($bookmark);
        $em->flush();

        return $this->json([
            'msg' => 'The bookmark was deleted successfully.',
        ]);
    }
}
