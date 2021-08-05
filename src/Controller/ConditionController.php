<?php

namespace App\Controller;

use App\Entity\Condition;
use App\Form\ConditionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[\Symfony\Component\Routing\Annotation\Route(path: '/api/condition')]
class ConditionController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/', methods: ['GET'])]
    public function index() : JsonResponse
    {
        return $this->json(
            $this
                ->getDoctrine()
                ->getRepository(Condition::class)
                ->findByBranch(
                    $this->getUser()->getBranch()
                )
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/new', methods: ['POST'])]
    public function new(Request $request) : JsonResponse
    {
        $condition = new Condition();
        $form = $this->createForm(ConditionType::class, $condition);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($condition);
            $em->flush();

            return $this->json($condition);
        }

        return $this->json([
            'msg' => 'Please enter a valid condition!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.getBranch() === condition.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['GET'])]
    public function show(Condition $condition) : JsonResponse
    {
        return $this->json($condition);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === condition.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['PUT'])]
    public function edit(Request $request, Condition $condition) : JsonResponse
    {
        $form = $this->createForm(ConditionType::class, $condition);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->json($condition);
        }

        return $this->json([
            'msg' => 'Please enter a valid condition!',
        ], 400);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') and user.getBranch() === condition.getBranch()")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/{id}', methods: ['DELETE'])]
    public function delete(Condition $condition) : JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($condition);
        $em->flush();

        return $this->json([
            'msg' => 'The condition was successfully deleted.',
        ]);
    }
}
