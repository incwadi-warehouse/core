<?php

namespace App\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Author;
use Symfony\Component\Form\DataTransformerInterface;

class AuthorToStringTransformer implements DataTransformerInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function transform($author): string
    {
        if (null === $author) {
            return '';
        }

        return $author->getSurname().','.$author->getFirstname();
    }

    public function reverseTransform($data): ?Author
    {
        if (!$data) {
            return null;
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
