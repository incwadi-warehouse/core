<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    protected int $genreId;

    protected int $conditionId;
    private string $bookId;

    public function setUp(): void
    {
        $this->buildClient();

        $request = $this->request('/api/v1/genre/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        $this->genreId = $request->id;

        $request = $this->request('/api/v1/condition/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        $this->conditionId = $request->id;

        $time = new \DateTime();
        $request = $this->request('/api/v1/book/new', 'POST', [], [
            'title' => 'title'.$time->getTimestamp(),
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 859,
            'cond' => $this->conditionId,
        ]);

        $this->bookId = $request->id;
    }

    public function tearDown(): void
    {
        $request = $this->request('/api/v1/book/'.$this->bookId, 'DELETE');

        $request = $this->request('/api/v1/genre/'.$this->genreId, 'DELETE');

        $this->assertEquals('The genre was deleted successfully.', $request->msg);

        $request = $this->request('/api/v1/condition/'.$this->conditionId, 'DELETE');

        $this->assertEquals('The condition was successfully deleted.', $request->msg);

        parent::tearDown();
    }

    public function testScenario()
    {
        // list
        $request = $this->request('/api/v1/tag/', 'GET');

        $this->assertIsArray($request);

        // new
        $request = $this->request('/api/v1/tag/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->books);

        $id = $request->id;

        // edit
        $request = $this->request('/api/v1/tag/'.$id, 'PUT', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->books);

        // show
        $request = $this->request('/api/v1/tag/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->books);

        // edit book
        $time = new \DateTime();

        $book = $this->request('/api/v1/book/'.$this->bookId, 'PUT', [], [
            'title' => 'title'.$time->getTimestamp(),
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 859,
            'cond' => $this->conditionId,
            'tags' => [$id],
        ]);

        // show book
        $request = $this->request('/api/v1/book/'.$book->id, 'GET');

        $this->assertEquals(1, count($request->tags));

        // delete
        $request = $this->request('/api/v1/tag/'.$id, 'DELETE');

        $this->assertEquals('The tag was deleted successfully.', $request->msg);
    }
}
