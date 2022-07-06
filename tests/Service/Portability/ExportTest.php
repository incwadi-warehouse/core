<?php

namespace App\Tests\Service\Portability;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Branch;
use App\Entity\Genre;
use App\Service\Portability\Export;
use PHPUnit\Framework\TestCase;

class ExportTest extends TestCase
{
    public function testImport()
    {
        $branch = new Branch();
        $branch->setName('Branch');

        $author = new Author();
        $author->setFirstname('firstname');
        $author->setSurname('surname');

        $genre = new Genre();
        $genre->setName('Foreign Language Books');

        $book1 = new Book();
        $book1->setBranch($branch);
        $book1->setAdded(new \DateTime('2017-10-06T00:00:00+0200'));
        $book1->setTitle('The Title');
        $book1->setAuthor($author);
        $book1->setGenre($genre);
        $book1->setPrice(25.00);
        $book1->setSold(false);
        $book1->setRemoved(false);
        $book1->setReleaseYear(2019);
        $book1->setCond(null);

        $book2 = new Book();
        $book2->setBranch($branch);
        $book2->setAdded(new \DateTime('2018-02-22T00:00:00+0100'));
        $book2->setTitle('The Title');
        $book2->setAuthor($author);
        $book2->setGenre($genre);
        $book2->setPrice(1.50);
        $book2->setSold(false);
        $book2->setRemoved(false);
        $book2->setReleaseYear(2019);
        $book2->setCond(null);

        $export = new Export();
        $books = $export->export([$book1, $book2]);

        $this->assertIsString($books);

        $expected = <<<EOF
branch;added;title;shortDescription;author.firstname;author.surname;genre;price;sold;soldOn;removed;removedOn;reserved;reservedAt;releaseYear;cond;reservation;recommendation;inventory;format
Branch;2017-10-06T00:00:00+0200;"The Title";;firstname;surname;"Foreign Language Books";25;0;;0;;0;;2019;;;0;;
Branch;2018-02-22T00:00:00+0100;"The Title";;firstname;surname;"Foreign Language Books";1.5;0;;0;;0;;2019;;;0;;

EOF;
        $this->assertEquals($expected, $books);
    }
}
