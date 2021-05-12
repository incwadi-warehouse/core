<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Util;

use Incwadi\Core\Entity\Author;
use Incwadi\Core\Entity\Branch;
use Incwadi\Core\Entity\Genre;
use Incwadi\Core\Entity\Staff;
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

        $staff = $this->getMockBuilder('\\Incwadi\\Core\\Repository\\StaffRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $staff
            ->expects($this->any())
            ->method('__call')
            ->with($this->equalTo('findOneByName'))
            ->willReturn(null);

        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $em->method('getRepository')
            ->will(
                $this->returnCallback(
                    function ($class) use ($branch, $author, $genre, $staff) {
                        switch ($class) {
                        case Branch::class:
                            return $branch;
                        case Author::class:
                            return $author;
                        case Genre::class:
                            return $genre;
                        case Staff::class:
                            return $staff;
                        }
                    }
                )
            );

        $import = new Import($em);
        $books = $import->import($this->getData());

        $this->assertIsArray($books);

        $this->assertTrue($books[0] instanceof \Incwadi\Core\Entity\Book);
        $this->assertEquals('branch 1', $books[0]->getBranch()->getName());
        $this->assertTrue($books[0]->getAdded() instanceof \DateTime);
        $this->assertEquals('The Title', $books[0]->getTitle());
        $this->assertEquals('firstname', $books[0]->getAuthor()->getFirstname());
        $this->assertEquals('surname', $books[0]->getAuthor()->getSurname());
        $this->assertEquals('genre 1', $books[0]->getGenre()->getName());
        $this->assertEquals(25.00, $books[0]->getPrice());
        $this->assertFalse($books[0]->getSold());
        $this->assertNull($books[0]->getSoldOn());
        $this->assertFalse($books[0]->getRemoved());
        $this->assertNull($books[0]->getRemovedOn());
        $this->assertEquals(2019, $books[0]->getReleaseYear());
        $this->assertEquals('paperback', $books[0]->getType());
        $this->assertEquals('admin', $books[0]->getLendTo()->getName());
        $this->assertTrue($books[0]->getLendOn() instanceof \DateTime);
        $this->assertEquals(null, $books[0]->getCond());

        $this->assertEquals(1.50, $books[1]->getPrice());
    }

    private function getData()
    {
        return <<<EOL
branch;added;title;author.firstname;author.surname;genre;price;sold;soldOn;removed;removedOn;releaseYear;type;lendTo;lendOn;cond
branch 1;2017-10-06T00:00:00+0200;"The Title";firstname;surname;"genre 1";25.00;0;;0;;2019;paperback;admin;2017-10-06T00:00:00+0200;
branch 2;2018-02-22T00:00:00+0100;"The Title";firstname;surname;"genre 2";1.50;0;;0;;2019;paperback;admin;2018-02-22T00:00:00+0100;
EOL;
    }
}
