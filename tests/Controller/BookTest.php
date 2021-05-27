<?php

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    protected int $genreId;

    protected int $conditionId;

    private array $tags = [];

    public function setUp(): void
    {
        $this->buildClient();

        $request = $this->request('/api/genre/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        $this->genreId = $request->id;

        $request = $this->request('/api/condition/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        $this->conditionId = $request->id;

        $request = $this->request('/api/tag/new', 'POST', [], [
            'name' => 'tag1',
        ]);
        $this->tags[] = $request->id;

        $request = $this->request('/api/tag/new', 'POST', [], [
            'name' => 'tag2',
        ]);
        $this->tags[] = $request->id;
    }

    public function tearDown(): void
    {
        $request = $this->request('/api/genre/'.$this->genreId, 'DELETE');

        $this->assertEquals('The genre was deleted successfully.', $request->msg);

        $request = $this->request('/api/condition/'.$this->conditionId, 'DELETE');

        $this->assertEquals('The condition was successfully deleted.', $request->msg);

        foreach ($this->tags as $tag) {
            $request = $this->request('/api/tag/'.$tag, 'DELETE');

            $this->assertEquals('The tag was deleted successfully.', $request->msg);
        }

        parent::tearDown();
    }

    public function testScenario()
    {
        // new
        $request = $this->request('/api/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 859,
            'cond' => $this->conditionId,
            'tags' => $this->tags,
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsString($request->id);
        if ($request->branch) {
            $this->assertIsInt($request->branch->id);
            $this->assertIsString($request->branch->name);
        }
        $this->assertIsInt($request->added);
        $this->assertEquals('title', $request->title);
        if (null !== $request->shortDescription) {
            $this->assertTrue(isset($request->shortDescription));
        }
        $this->assertEquals('firstname', $request->author->firstname);
        $this->assertEquals('surname', $request->author->surname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('1.00', $request->price);
        $this->assertFalse($request->sold);
        $this->assertNull($request->soldOn);
        $this->assertFalse($request->removed);
        $this->assertNull($request->removedOn);
        $this->assertFalse($request->reserved);
        $this->assertNull($request->reservedAt);
        $this->assertEquals(2019, $request->releaseYear);
        $this->assertEquals('paperback', $request->type);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);
        $this->assertIsInt($request->condition->id);
        $this->assertEquals(2, count($request->tags));
        $this->assertIsInt($request->tags[0]->id);
        $this->assertIsString($request->tags[0]->name);

        $id = $request->id;

        // edit
        $request = $this->request('/api/book/'.$id, 'PUT', [], [
            'title' => 'book',
            'author' => 'surname1,firstname1',
            'genre' => $this->genreId,
            'price' => '2.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 4758,
            'cond' => $this->conditionId,
            'tags' => $this->tags,
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsString($request->id);
        if ($request->branch) {
            $this->assertIsInt($request->branch->id);
            $this->assertIsString($request->branch->name);
        }
        $this->assertIsInt($request->added);
        $this->assertEquals('book', $request->title);
        if (null !== $request->shortDescription) {
            $this->assertTrue(isset($request->shortDescription));
        }
        $this->assertEquals('firstname1', $request->author->firstname);
        $this->assertEquals('surname1', $request->author->surname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertFalse($request->sold);
        $this->assertNull($request->soldOn);
        $this->assertFalse($request->removed);
        $this->assertNull($request->removedOn);
        $this->assertFalse($request->reserved);
        $this->assertNull($request->reservedAt);
        $this->assertEquals(2019, $request->releaseYear);
        $this->assertEquals('paperback', $request->type);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);
        $this->assertIsInt($request->condition->id);
        $this->assertEquals(2, count($request->tags));
        $this->assertIsInt($request->tags[0]->id);
        $this->assertIsString($request->tags[0]->name);

        // sell
        $request = $this->request('/api/book/sell/'.$id, 'PUT');
        $this->assertTrue($request->sold);

        $request = $this->request('/api/book/sell/'.$id, 'PUT');
        $this->assertFalse($request->sold);

        // remove
        $request = $this->request('/api/book/remove/'.$id, 'PUT');
        $this->assertTrue($request->removed);

        $request = $this->request('/api/book/remove/'.$id, 'PUT');
        $this->assertFalse($request->removed);

        // reserve
        $request = $this->request('/api/book/reserve/'.$id, 'PUT');
        $this->assertTrue($request->reserved);

        $request = $this->request('/api/book/reserve/'.$id, 'PUT');
        $this->assertFalse($request->reserved);

        // show
        $request = $this->request('/api/book/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertIsString($request->id);
        if ($request->branch) {
            $this->assertIsInt($request->branch->id);
            $this->assertIsString($request->branch->name);
        }
        $this->assertIsInt($request->added);
        $this->assertEquals('book', $request->title);
        if (null !== $request->shortDescription) {
            $this->assertTrue(isset($request->shortDescription));
        }
        $this->assertEquals('firstname1', $request->author->firstname);
        $this->assertEquals('surname1', $request->author->surname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertFalse($request->sold);
        $this->assertNull($request->soldOn);
        $this->assertFalse($request->removed);
        $this->assertNull($request->removedOn);
        $this->assertFalse($request->reserved);
        $this->assertNull($request->reservedAt);
        $this->assertEquals(2019, $request->releaseYear);
        $this->assertEquals('paperback', $request->type);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);
        $this->assertIsInt($request->condition->id);
        $this->assertEquals(2, count($request->tags));
        $this->assertIsInt($request->tags[0]->id);
        $this->assertIsString($request->tags[0]->name);

        // find
        $request = $this->request('/api/book/find', 'GET', [
            'options' => json_encode(['term' => 'book']),
        ]);

        $this->assertIsObject($request);
        $this->assertTrue(isset($request->books[0]->id));
        if ($request->books[0]->branch) {
            $this->assertIsInt($request->books[0]->branch->id);
            $this->assertIsString($request->books[0]->branch->name);
        }
        $this->assertIsString($request->books[0]->id);
        $this->assertIsInt($request->books[0]->added);
        $this->assertIsString($request->books[0]->title);
        if (null !== $request->books[0]->shortDescription) {
            $this->assertTrue(isset($request->books[0]->shortDescription));
        }
        if (null !== $request->books[0]->author) {
            $this->assertIsString($request->books[0]->author->firstname);
            $this->assertIsString($request->books[0]->author->surname);
        }
        if ($request->books[0]->genre) {
            $this->assertIsInt($request->books[0]->genre->id);
            $this->assertIsString($request->books[0]->genre->name);
        }
        $this->assertNotEmpty($request->books[0]->price);
        $this->assertFalse($request->books[0]->sold);
        if (null !== $request->books[0]->soldOn) {
            $this->assertIsString($request->books[0]->soldOn);
        }
        $this->assertIsBool($request->books[0]->removed);
        $this->assertNull($request->books[0]->removedOn);
        $this->assertFalse($request->books[0]->reserved);
        $this->assertNull($request->books[0]->reservedAt);
        $this->assertIsInt($request->books[0]->releaseYear);
        $this->assertEquals('paperback', $request->books[0]->type);
        if (null !== $request->books[0]->lendTo) {
            $this->assertNotEmpty($request->books[0]->lendTo);
        }
        if (null !== $request->books[0]->lendOn) {
            $this->assertNotEmpty($request->books[0]->lendOn);
        }
        if (null !== $request->books[0]->condition) {
            $this->assertIsInt($request->books[0]->condition->id);
        }
        if (count($request->books[0]->tags) >= 1) {
            $this->assertIsInt($request->books[0]->tags[0]->id);
            $this->assertIsString($request->books[0]->tags[0]->name);
        }

        // delete
        $request = $this->request('/api/book/'.$id, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
    }

    public function testDuplicate()
    {
        $request = $this->request('/api/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 4758,
            'cond' => $this->conditionId,
        ]);

        $this->assertIsString($request->id);

        $request = $this->request('/api/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 4758,
            'cond' => $this->conditionId,
        ], 409);

        $this->assertEquals('Book not saved, because it exists already!', $request->msg);
    }
}
