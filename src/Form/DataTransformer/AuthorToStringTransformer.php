<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Author;
use Symfony\Component\Form\DataTransformerInterface;

class AuthorToStringTransformer implements DataTransformerInterface
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function transform($author)
    {
        if ($author === null) {
            return '';
        }

        return $author->getLastname() . ',' . $author->getFirstname();
    }

    public function reverseTransform($data)
    {
        if (!$data) {
            return;
        }

        $data = explode(',', $data);

        $author = $this->em->getRepository(Author::class)->findOneBy(
            [
                'firstname' => $data[1],
                'lastname' => $data[0]
            ]
        );

        if ($author) {
            return $author;
        }

        $author = new Author();
        $author->setFirstname($data[1]);
        $author->setLastname($data[0]);

        return $author;
    }
}
