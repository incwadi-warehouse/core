<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    protected $genreId;

    protected $conditionId;

    public function setUp(): void
    {
        $this->buildClient();

        $request = $this->request('/v1/genre/new', 'POST', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        $this->genreId = $request->id;

        $request = $this->request('/v1/condition/new', 'POST', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        $this->conditionId = $request->id;
    }

    public function tearDown(): void
    {
        $request = $this->request('/v1/genre/'.$this->genreId, 'DELETE');

        $this->assertEquals('The genre was successfully deleted.', $request->msg);

        $request = $this->request('/v1/condition/'.$this->conditionId, 'DELETE');

        $this->assertEquals('The condition was successfully deleted.', $request->msg);

        parent::tearDown();
    }

    public function testScenario()
    {
        // index
        $request = $this->request('/v1/book/', 'GET', [], []);

        $this->assertEquals([], $request);

        // new
        $request = $this->request('/v1/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 859,
            'cond' => $this->conditionId
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        if ($request->branch) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('title', $request->title);
        $this->assertEquals('firstname', $request->author->firstname);
        $this->assertEquals('surname', $request->author->surname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('1.00', $request->price);
        $this->assertFalse($request->sold);
        $this->assertNull($request->soldOn);
        $this->assertFalse($request->removed);
        $this->assertNull($request->removedOn);
        $this->assertEquals(2019, $request->releaseYear);
        $this->assertEquals('paperback', $request->type);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);
        $this->assertInternalType('int', $request->condition->id);

        $id = $request->id;

        // edit
        $request = $this->request('/v1/book/'.$id, 'PUT', [], [
            'title' => 'book',
            'author' => 'surname1,firstname1',
            'genre' => $this->genreId,
            'price' => '2.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 4758,
            'cond' => $this->conditionId
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        if ($request->branch) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('book', $request->title);
        $this->assertEquals('firstname1', $request->author->firstname);
        $this->assertEquals('surname1', $request->author->surname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertFalse($request->sold);
        $this->assertNull($request->soldOn);
        $this->assertFalse($request->removed);
        $this->assertNull($request->removedOn);
        $this->assertEquals(2019, $request->releaseYear);
        $this->assertEquals('paperback', $request->type);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);
        $this->assertInternalType('int', $request->condition->id);

        // sell
        $request = $this->request('/v1/book/sell/'.$id, 'PUT');
        $this->assertTrue($request->sold);

        $request = $this->request('/v1/book/sell/'.$id, 'PUT');
        $this->assertFalse($request->sold);

        // remove
        $request = $this->request('/v1/book/remove/'.$id, 'PUT');
        $this->assertTrue($request->removed);

        $request = $this->request('/v1/book/remove/'.$id, 'PUT');
        $this->assertFalse($request->removed);

        // show
        $request = $this->request('/v1/book/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        if ($request->branch) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('book', $request->title);
        $this->assertEquals('firstname1', $request->author->firstname);
        $this->assertEquals('surname1', $request->author->surname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertFalse($request->sold);
        $this->assertNull($request->soldOn);
        $this->assertFalse($request->removed);
        $this->assertNull($request->removedOn);
        $this->assertEquals(2019, $request->releaseYear);
        $this->assertEquals('paperback', $request->type);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);
        $this->assertInternalType('int', $request->condition->id);

        // find
        $request = $this->request('/v1/book/find', 'GET', [
            'term' => 'book'
        ]);

        $this->assertInternalType('int', $request->counter);
        $this->assertInternalType('array', $request->books);
        $this->assertTrue(isset($request->books[0]->id));
        if ($request->books[0]->branch) {
            $this->assertInternalType('int', $request->books[0]->branch->id);
            $this->assertInternalType('string', $request->books[0]->branch->name);
        }
        $this->assertInternalType('integer', $request->books[0]->id);
        $this->assertInternalType('integer', $request->books[0]->added);
        $this->assertInternalType('string', $request->books[0]->title);
        if (null !== $request->books[0]->author) {
            $this->assertInternalType('string', $request->books[0]->author->firstname);
            $this->assertInternalType('string', $request->books[0]->author->surname);
        }
        if ($request->books[0]->genre) {
            $this->assertInternalType('integer', $request->books[0]->genre->id);
            $this->assertInternalType('string', $request->books[0]->genre->name);
        }
        $this->assertNotEmpty($request->books[0]->price);
        $this->assertFalse($request->books[0]->sold);
        if (null !== $request->books[0]->soldOn) {
            $this->assertInternalType('string', $request->books[0]->soldOn);
        }
        $this->assertFalse($request->books[0]->removed);
        $this->assertNull($request->books[0]->removedOn);
        $this->assertInternalType('integer', $request->books[0]->releaseYear);
        $this->assertEquals('paperback', $request->books[0]->type);
        if (null !== $request->books[0]->lendTo) {
            $this->assertNotEmpty($request->books[0]->lendTo);
        }
        if (null !== $request->books[0]->lendOn) {
            $this->assertNotEmpty($request->books[0]->lendOn);
        }
        $this->assertInternalType('int', $request->books[0]->condition->id);

        // delete
        $request = $this->request('/v1/book/'.$id, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
    }

    public function testDuplicate()
    {
        $request = $this->request('/v1/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 4758,
            'cond' => $this->conditionId
        ]);

        $this->assertInternalType('int', $request->id);

        $request = $this->request('/v1/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 4758,
            'cond' => $this->conditionId
        ], 409);

        $this->assertEquals('Book not saved, because it exists already!', $request->msg);
    }
}
