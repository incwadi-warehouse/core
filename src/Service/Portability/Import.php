<?php

namespace App\Service\Portability;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Branch;
use App\Entity\Genre;
use App\Entity\Staff;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/*
 * Deprecated
 */
class Import implements ImportInterface
{
    protected EntityManagerInterface $em;

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
                'csv_delimiter' => ';',
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
            null !== $book->getGenre() ? $book->getGenre()->setBranch($book->getBranch()) : null;
            $book->setPrice($item['price']);
            $book->setSold($item['sold']);
            $book->setReleaseYear($item['releaseYear']);

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
                'surname' => $data['surname'],
            ]
        );
        if (null !== $existing) {
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

    protected function staff(string $data): ?Staff
    {
        if ('' === $data) {
            return null;
        }

        $existing = $this->em->getRepository(Staff::class)->findOneByName($data);
        if ($existing) {
            return $existing;
        }

        $staff = new Staff();
        $staff->setName($data);

        $this->em->persist($staff);
        $this->em->flush();

        return $staff;
    }
}
