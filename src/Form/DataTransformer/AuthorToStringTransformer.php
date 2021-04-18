<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Author;
use Symfony\Component\Form\DataTransformerInterface;

class AuthorToStringTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function transform($author)
    {
        if (null === $author) {
            return '';
        }

        return $author->getSurname().','.$author->getFirstname();
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
                'surname' => $data[0],
            ]
        );

        if (null !== $author) {
            return $author;
        }

        $author = new Author();
        $author->setFirstname($data[1]);
        $author->setSurname($data[0]);

        return $author;
    }
}
