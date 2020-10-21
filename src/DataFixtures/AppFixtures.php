<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Incwadi\Core\Entity\Author;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Entity\Genre;
use Incwadi\Core\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $branch = new Branch();
        $branch->setName('test');
        $manager->persist($branch);

        $user = new User();
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                'password'
            )
        );
        $user->setBranch($branch);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                'password'
            )
        );
        $user->setBranch($branch);
        $manager->persist($user);

        $author = new Author();
        $author->setFirstname('John');
        $author->setSurname('Doe');
        $manager->persist($author);

        $genre = new Genre();
        $genre->setName('Crime');
        $genre->setBranch($branch);
        $manager->persist($genre);

        $book = new Book();
        $book->setBranch($branch);
        $book->setTitle('Demo Book 1');
        $book->setAuthor($author);
        $book->setGenre($genre);
        $book->setPrice(1.00);
        $book->setReleaseYear(2020);
        $manager->persist($book);

        $book = new Book();
        $book->setBranch($branch);
        $book->setTitle('Demo Book 2');
        $book->setAuthor($author);
        $book->setGenre($genre);
        $book->setPrice(2.00);
        $book->setReleaseYear(2020);
        $manager->persist($book);

        $book = new Book();
        $book->setBranch($branch);
        $book->setTitle('Demo Book 3');
        $book->setAuthor($author);
        $book->setGenre($genre);
        $book->setPrice(3.00);
        $book->setReleaseYear(2020);
        $manager->persist($book);

        $manager->flush();
    }
}
