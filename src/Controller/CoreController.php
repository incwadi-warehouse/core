<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Controller;

use Incwadi\Core\Form\ProfilePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CoreController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="index")
     * @Route("/v1", methods={"GET"}, name="index2")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): JsonResponse
    {
        return $this->json([]);
    }

    /**
     * @Route("/api/v1/me", methods={"GET"}, name="me")
     * @Security("is_granted('ROLE_USER')")
     */
    public function me(): JsonResponse
    {
        return $this->json([
            'id' => $this->getUser()->getId(),
            'username' => $this->getUser()->getUsername(),
            'roles' => $this->getUser()->getRoles(),
            'branch' => $this->getUser()->getBranch(),
            'isUser' => $this->isGranted('ROLE_USER'),
            'isAdmin' => $this->isGranted('ROLE_ADMIN')
        ]);
    }

    /**
     * @Route("/api/v1/password", methods={"PUT"}, name="password")
     * @Security("is_granted('ROLE_USER')")
     */
    public function password(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfilePasswordType::class, $user);

        $form->submit(
            json_decode(
                $request->getContent(),
                true
            )
        );
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $encoder->encodePassword(
                    $user,
                    $user->getPassword()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            return $this->json([
                'msg' => 'The password was successfully changed!'
            ]);
        }

        return $this->json([
                'msg' => 'The password could not be changed!'
        ], 400);
    }
}
