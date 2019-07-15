<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Util;

use Incwadi\Core\Entity\Author;
use Incwadi\Core\Entity\Book;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Entity\Customer;
use Incwadi\Core\Entity\Genre;
use Incwadi\Core\Util\Export;
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

        $customer = new Customer();
        $customer->setName('admin');

        $book1 = new Book();
        $book1->setBranch($branch);
        $book1->setAdded(new \DateTime('2017-10-06T00:00:00+0200'));
        $book1->setTitle('The Title');
        $book1->setAuthor($author);
        $book1->setGenre($genre);
        $book1->setPrice(25.00);
        $book1->setStocked(true);
        $book1->setYearOfPublication(2019);
        $book1->setType('paperback');
        $book1->setPremium(false);
        $book1->setLendTo($customer);
        $book1->setLendOn(new \DateTime('2017-07-06T00:00:00+0200'));

        $book2 = new Book();
        $book2->setBranch($branch);
        $book2->setAdded(new \DateTime('2018-02-22T00:00:00+0100'));
        $book2->setTitle('The Title');
        $book2->setAuthor($author);
        $book2->setGenre($genre);
        $book2->setPrice(1.50);
        $book2->setStocked(true);
        $book2->setYearOfPublication(2019);
        $book2->setType('paperback');
        $book2->setPremium(false);
        $book2->setLendTo(null);
        $book2->setLendOn(null);

        $export = new Export();
        $books = $export->export([$book1, $book2]);

        $this->assertInternalType('string', $books);

        $expected = <<<EOF
branch;added;title;author.firstname;author.surname;genre;price;stocked;yearOfPublication;type;premium;lendTo;lendOn
Branch;2017-10-06T00:00:00+0200;"The Title";firstname;surname;"Foreign Language Books";25;1;2019;paperback;0;admin;2017-07-06T00:00:00+0200
Branch;2018-02-22T00:00:00+0100;"The Title";firstname;surname;"Foreign Language Books";1.5;1;2019;paperback;0;;

EOF;
        $this->assertEquals($expected, $books);
    }
}
