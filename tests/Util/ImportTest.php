<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Util;

use Incwadi\Core\Entity\Author;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Entity\Customer;
use Incwadi\Core\Entity\Genre;
use Incwadi\Core\Util\Import;
use PHPUnit\Framework\TestCase;

class ImportTest extends TestCase
{
    public function testImport()
    {
        $branch = $this->getMockBuilder('\\Incwadi\\Core\\Repository\\BranchRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $branch
            ->expects($this->any())
            ->method('__call')
            ->with($this->equalTo('findOneByName'))
            ->willReturn(null);

        $author = $this->getMockBuilder('\\Incwadi\\Core\\Repository\\AuthorRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $author
            ->expects($this->any())
            ->method('__call')
            ->with($this->equalTo('findOneBy'))
            ->willReturn(null);

        $genre = $this->getMockBuilder('\\Incwadi\\Core\\Repository\\GenreRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $genre
            ->expects($this->any())
            ->method('__call')
            ->with($this->equalTo('findOneByName'))
            ->willReturn(null);

        $customer = $this->getMockBuilder('\\Incwadi\\Core\\Repository\\CustomerRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $customer
            ->expects($this->any())
            ->method('__call')
            ->with($this->equalTo('findOneByName'))
            ->willReturn(null);

        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $em->method('getRepository')
            ->will(
                $this->returnCallback(function ($class) use ($branch, $author, $genre, $customer) {
                    switch ($class) {
                        case Branch::class:
                            return $branch;
                        case Author::class:
                            return $author;
                        case Genre::class:
                            return $genre;
                        case Customer::class:
                            return $customer;
                    }
                }
            ));

        $import = new Import($em);
        $books = $import->import($this->getData());

        $this->assertInternalType('array', $books);

        $this->assertTrue($books[0] instanceof \Incwadi\Core\Entity\Book);
        $this->assertEquals('branch 1', $books[0]->getBranch()->getName());
        $this->assertEquals(new \DateTime('6.10.2017'), $books[0]->getAdded());
        $this->assertEquals('The Title', $books[0]->getTitle());
        $this->assertEquals('firstname', $books[0]->getAuthor()->getFirstname());
        $this->assertEquals('lastname', $books[0]->getAuthor()->getLastname());
        $this->assertEquals('genre 1', $books[0]->getGenre()->getName());
        $this->assertEquals(25.00, $books[0]->getPrice());
        $this->assertTrue($books[0]->getStocked());
        $this->assertEquals(2019, $books[0]->getYearOfPublication());
        $this->assertEquals('paperback', $books[0]->getType());
        $this->assertFalse($books[0]->getPremium());
        $this->assertEquals('admin', $books[0]->getLendTo()->getName());
        $this->assertEquals(new \DateTime('6.10.2017'), $books[0]->getLendOn());

        $this->assertEquals(1.50, $books[1]->getPrice());
    }

    private function getData()
    {
        return <<<EOL
branch;added;title;author.firstname;author.lastname;genre;price;stocked;yearOfPublication;type;premium;lendTo;lendOn
branch 1;2017-10-06T00:00:00+0200;"The Title";firstname;lastname;"genre 1";25.00;1;2019;paperback;0;admin;2017-10-06T00:00:00+0200
branch 2;2018-02-22T00:00:00+0100;"The Title";firstname;lastname;"genre 2";1.50;1;2019;paperback;0;admin;2018-02-22T00:00:00+0100
EOL;
    }
}
