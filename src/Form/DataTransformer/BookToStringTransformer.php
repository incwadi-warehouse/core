<?php

namespace App\Form\DataTransformer;

use App\Repository\BookRepository;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BookToStringTransformer implements DataTransformerInterface
{
    public function __construct(private readonly BookRepository $bookRepository)
    {
    }

    public function transform($books): mixed
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

    public function reverseTransform($data): mixed
    {
        if (!$data) {
            return [];
        }

        $data = explode(',', (string) $data);
        $books = [];
        foreach ($data as $item) {
            $books[] = $this->bookRepository->find(
                $item
            );
        }

        return $books;
    }
}
