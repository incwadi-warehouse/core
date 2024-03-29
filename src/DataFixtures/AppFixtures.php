<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Branch;
use App\Entity\Condition;
use App\Entity\Format;
use App\Entity\Genre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
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
            $this->passwordEncoder->hashPassword(
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
            $this->passwordEncoder->hashPassword(
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

        $condition = new Condition();
        $condition->setName('Condition 1');
        $condition->setBranch($branch);

        $manager->persist($condition);

        $format = new Format();
        $format->setName('Format 1');
        $format->setBranch($branch);

        $manager->persist($format);

        $manager->flush();
    }
}
