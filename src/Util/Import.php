<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Util;

use Doctrine\ORM\EntityManagerInterface;
use Incwadi\Core\Entity\Author;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Entity\Customer;
use Incwadi\Core\Entity\Genre;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Import implements ImportInterface
{
    protected $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function import(string $data): array
    {
        $serializer = new Serializer(
            [new ObjectNormalizer()],
            [new CsvEncoder()]
        );
        $item = $serializer->decode(
            $data,
            'csv',
            [
                'csv_delimiter' => ';'
            ]
        );

        $books = [];
        foreach ($item as $item) {
            $book = new Book();
            $book->setBranch($this->branch($item['branch']));
            $book->setAdded(new \DateTime($item['added']));
            $book->setTitle($item['title']);
            $book->setAuthor($this->author($item['author']));
            $book->setGenre($this->genre($item['genre']));
            $book->setPrice($item['price']);
            $book->setStocked($item['stocked']);
            $book->setReleaseYear($item['releaseYear']);
            $book->setType($item['type']);
            $book->setPremium($item['premium']);
            $book->setLendTo($this->customer($item['lendTo']));
            $book->setLendOn(new \DateTime($item['lendOn']));

            $books[] = $book;

            $this->em->persist($book);
        }
        $this->em->flush();

        return $books;
    }

    protected function branch(string $data): Branch
    {
        $existing = $this->em->getRepository(Branch::class)->findOneByName($data);
        if ($existing) {
            return $existing;
        }

        $branch = new Branch();
        $branch->setName($data);

        $this->em->persist($branch);
        $this->em->flush();

        return $branch;
    }

    protected function author(array $data): author
    {
        $existing = $this->em->getRepository(Author::class)->findOneBy(
            [
                'firstname' => $data['firstname'],
                'surname' => $data['surname']
            ]
        );
        if ($existing) {
            return $existing;
        }

        $author = new Author();
        $author->setFirstname($data['firstname']);
        $author->setSurname($data['surname']);

        $this->em->persist($author);
        $this->em->flush();

        return $author;
    }

    protected function genre(string $data): Genre
    {
        $existing = $this->em->getRepository(Genre::class)->findOneByName($data);
        if ($existing) {
            return $existing;
        }

        $genre = new Genre();
        $genre->setName($data);

        $this->em->persist($genre);
        $this->em->flush();

        return $genre;
    }

    protected function customer(string $data): ?Customer
    {
        if ($data === '') {
            return null;
        }

        $existing = $this->em->getRepository(Customer::class)->findOneByName($data);
        if ($existing) {
            return $existing;
        }

        $customer = new Customer();
        $customer->setName($data);

        $this->em->persist($customer);
        $this->em->flush();

        return $customer;
    }
}