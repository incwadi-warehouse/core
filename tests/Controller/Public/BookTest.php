<?php

namespace App\Tests\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    private int $branch;

    private string $book;

    public function setUp(): void
    {
        $this->buildClient();

        // me
        $request = $this->request('/api/me', 'GET');

        $this->branch = $request->branch->id;

        // new book
        $date = new \DateTime();
        $request = $this->request('/api/book/new', 'POST', [], [
            'title' => 'title '.$date->getTimestamp(),
            'author' => 'surname,firstname',
            'genre' => null,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'added' => 859,
            'cond' => null,
            'tags' => null,
        ]);

        $this->assertTrue(isset($request->id));

        $this->book = $request->id;
    }

    public function tearDown(): void
    {
        // delete book
        $request = $this->request('/api/book/' . $this->book, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);

        parent::tearDown();
    }

    public function testScenario()
    {
        // find
        $request = $this->request('/api/public/book/find', 'GET', [
            'options' => json_encode(['term' => 'book', 'filter' => []]),
        ]);

        $this->assertIsArray($request->books);
        $this->assertEquals(13, count((array) $request->books[0]));
        $this->assertIsString($request->books[0]->id);
        $this->assertIsString($request->books[0]->currency);
        $this->assertIsString($request->books[0]->title);
        if (null !== $request->books[0]->shortDescription) {
            $this->assertTrue(isset($request->books[0]->shortDescription));
        }
        $this->assertIsString($request->books[0]->authorFirstname);
        $this->assertIsString($request->books[0]->authorSurname);
        $this->assertIsString($request->books[0]->genre);
        $this->assertNotEmpty($request->books[0]->price);
        $this->assertIsInt($request->books[0]->releaseYear);
        $this->assertIsString($request->books[0]->branchName);
        if (null !== $request->books[0]->branchOrdering) {
            $this->assertIsString($request->books[0]->branchOrdering);
        }
        if (null !== $request->books[0]->cond) {
            $this->assertIsString($request->books[0]->cond);
        }

        // show
        $request = $this->request('/api/public/book/' . $this->book, 'GET');

        $this->assertEquals(13, count((array) $request));
        $this->assertIsString($request->id);
        $this->assertIsString($request->currency);
        $this->assertIsString($request->title);
        if (null !== $request->shortDescription) {
            $this->assertTrue(isset($request->shortDescription));
        }
        $this->assertIsString($request->authorFirstname);
        $this->assertIsString($request->authorSurname);
        if(null !== $request->genre) {
            $this->assertIsString($request->genre);
        }
        $this->assertNotEmpty($request->price);
        $this->assertIsInt($request->releaseYear);
        $this->assertIsString($request->branchName);
        if (null !== $request->branchOrdering) {
            $this->assertIsString($request->branchOrdering);
        }
        if (null !== $request->cond) {
            $this->assertIsString($request->cond);
        }

        // recommendation
        $request = $this->request('/api/public/book/recommendation/'.$this->branch, 'GET');

        $this->assertIsArray($request->books);
        $this->assertIsInt($request->counter);
    }
}
