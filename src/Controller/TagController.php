<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route(path: '/api/tag')]
class TagController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/', methods: ['GET'])]
    public function index(ManagerRegistry $manager): JsonResponse
    {
        return $this->json(
            $manager->getRepository(Tag::class)->findByBranch(
                $this->getUser()->getBranch()
            )
        );
    }

    /**
     * @Security("is_granted('ROLE_USER') and tag.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['GET'])]
    public function show(Tag $tag): JsonResponse
    {
        return $this->json($tag);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[Route(path: '/new', methods: ['POST'])]
    public function new(Request $request, ManagerRegistry $manager): JsonResponse
    {
        $content = json_decode(
            $request->getContent(),
            true
        );
        $tag = $manager->getRepository(Tag::class)
            ->findOneByName($content['name']);
        if ($tag) {
            return $this->json($tag);
        }

        $tag = new Tag();
        $tag->setBranch($this->getUser()->getBranch());

        $form = $this->createForm(TagType::class, $tag);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->json($tag);
        }

        return $this->json([
            'msg' => 'Please enter a valid tag!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and tag.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Tag $tag, ManagerRegistry $manager): JsonResponse
    {
        $form = $this->createForm(TagType::class, $tag);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->flush();

            return $this->json($tag);
        }

        return $this->json([
            'msg' => 'Please enter a valid tag!',
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and tag.getBranch() === user.getBranch()")
     */
    #[Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Tag $tag, ManagerRegistry $manager): JsonResponse
    {
        $em = $manager->getManager();
        foreach ($tag->getBooks() as $book) {
            $tag->removeBook($book);
        }

        $em->remove($tag);
        $em->flush();

        return $this->json([
            'msg' => 'The tag was deleted successfully.',
        ]);
    }
}
