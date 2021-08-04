<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Branch;
use App\Entity\Genre;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $branch = new Branch();
        $branch->setName('test');
        $branch->setPublic(true);

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

        $books = 50;
        for ($i = 1; $i <= $books; ++$i) {
            $book = new Book();
            $book->setBranch($branch);
            $book->setTitle('Demo Book '.$i);
            $book->setAuthor($author);
            $book->setGenre($genre);
            $book->setPrice($i);
            $book->setReleaseYear(2020);
            $manager->persist($book);
        }

        $manager->flush();
    }
}
