<?php

namespace App\Form\DataTransformer;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BookToStringTransformer implements DataTransformerInterface
{
    public function __construct(private EntityManagerInterface $em, private TokenStorageInterface $tokenStorage)
    {
    }

    public function transform($books): string
    {
        if (null === $books) {
            return '';
        }

        $list = [];
        foreach ($books as $book) {
            $list[] = $book->getId();
        }

        return implode(',', $list);
    }

    public function reverseTransform($data): array
    {
        if (!$data) {
            return [];
        }

        $data = explode(',', $data);
        $books = [];
        foreach ($data as $item) {
            $books[] = $this->em->getRepository(Book::class)->find(
                $item
            );
        }

        return $books;
    }
}
