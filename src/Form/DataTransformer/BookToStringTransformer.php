<?php

namespace Incwadi\Core\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Book;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BookToStringTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
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
            $found = $this->em->getRepository(Book::class)->find(
                $item
            );
            if ($this->tokenStorage->getToken()->getUser()->getBranch() === $found->getBranch()) {
                $books[] = $found;
            }
        }

        return $books;
    }
}
