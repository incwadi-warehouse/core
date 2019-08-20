<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/v1/password", methods={"PUT"}, name="password")
     * @Security("is_granted('ROLE_USER')")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        $user = $this->getUser();
        $password = json_decode(
            $request->getContent(),
            true
        );
        $user->setPassword(
            $encoder->encodePassword(
                $user,
                $password['password']
            )
        );
        $this->getDoctrine()->getManager()->flush();

        return $this->json(
            [
                'msg' => 'Password changed successfully!'
                ]
            );
    }
}
