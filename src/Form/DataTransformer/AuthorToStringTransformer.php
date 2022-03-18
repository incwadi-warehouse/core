<?php

namespace App\Form\DataTransformer;

use App\Repository\AuthorRepository;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class AuthorToStringTransformer implements DataTransformerInterface
{
    public function __construct(private readonly AuthorRepository $authorRepository)
    {
    }

    public function transform($author): mixed
    {
        if (!$author instanceof Author) {
            return '';
        }

        return $author->getSurname().','.$author->getFirstname();
    }

    public function reverseTransform($data): mixed
    {
        if (!$data) {
            return null;
        }

        $data = explode(',', (string) $data);

        $author = $this->authorRepository->findOneBy(
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
