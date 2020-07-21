<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Entity\Tag;
use Incwadi\Core\Form\TagType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/tag")
 */
class TagController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json(
            $this->getDoctrine()->getRepository(Tag::class)->findByBranch(
                $this->getUser()->getBranch()
            )
        );
    }

    /**
     * @Route("/{id}", methods={"GET"})
     * @Security("is_granted('ROLE_USER') and tag.getBranch() === user.getBranch()")
     */
    public function show(Tag $tag): JsonResponse
    {
        return $this->json($tag);
    }

    /**
     * @Route("/new", methods={"POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): JsonResponse
    {
        $content = json_decode(
            $request->getContent(),
            true
        );
        $tag = $this->getDoctrine()->getRepository(Tag::class)
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            return $this->json($tag);
        }

        return $this->json([
            'msg' => 'Please enter a valid tag!',
        ], 400);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN') and tag.getBranch() === user.getBranch()")
     */
    public function edit(Request $request, Tag $tag): JsonResponse
    {
        $form = $this->createForm(TagType::class, $tag);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($tag);
        }

        return $this->json([
            'msg' => 'Please enter a valid tag!',
        ]);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     * @Security("is_granted('ROLE_ADMIN') and tag.getBranch() === user.getBranch()")
     */
    public function delete(Tag $tag): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
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
