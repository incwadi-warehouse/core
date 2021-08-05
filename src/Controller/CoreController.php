<?php

namespace App\Controller;

use App\Form\ProfilePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[\Symfony\Component\Routing\Annotation\Route(path: '/api')]
class CoreController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/me', methods: ['GET'])]
    public function me() : JsonResponse
    {
        return $this->json([
            'id' => $this->getUser()->getId(),
            'username' => $this->getUser()->getUsername(),
            'roles' => $this->getUser()->getRoles(),
            'branch' => $this->getUser()->getBranch(),
            'isUser' => $this->isGranted('ROLE_USER'),
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/password', methods: ['PUT'])]
    public function password(Request $request, UserPasswordEncoderInterface $encoder) : JsonResponse
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
                'id' => $this->getUser()->getId(),
                'username' => $this->getUser()->getUsername(),
                'roles' => $this->getUser()->getRoles(),
                'branch' => $this->getUser()->getBranch(),
                'isUser' => $this->isGranted('ROLE_USER'),
                'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            ]);
        }

        return $this->json([
                'msg' => 'The password could not be changed!',
        ], 400);
    }
}
