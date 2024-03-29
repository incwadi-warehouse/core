<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagTest extends WebTestCase
{
    use \Baldeweg\Bundle\ApiBundle\ApiTestTrait;

    protected int $genreId;

    protected int $conditionId;
    protected int $formatId;
    private string $bookId;

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

        $request = $this->request('/api/format/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));

        $this->formatId = $request->id;

        $time = new \DateTime();
        $request = $this->request('/api/book/new', 'POST', [], [
            'title' => 'title'.$time->getTimestamp(),
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'added' => 859,
            'cond' => $this->conditionId,
            'format' => $this->formatId
        ]);

        $this->bookId = $request->id;
    }

    public function tearDown(): void
    {
        $this->request('/api/book/'.$this->bookId, 'DELETE');

        $request = $this->request('/api/genre/'.$this->genreId, 'DELETE');

        $this->assertEquals('The genre was deleted successfully.', $request->msg);

        $request = $this->request('/api/condition/'.$this->conditionId, 'DELETE');

        $this->assertEquals('The condition was successfully deleted.', $request->msg);

        $request = $this->request('/api/format/' . $this->formatId, 'DELETE');

        $this->assertEquals('The format was deleted successfully.', $request->msg);

        parent::tearDown();
    }

    public function testScenario()
    {
        // list
        $request = $this->request('/api/tag/', 'GET');

        $this->assertIsArray($request);

        // new
        $request = $this->request('/api/tag/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->books);

        $id = $request->id;

        // edit
        $request = $this->request('/api/tag/'.$id, 'PUT', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->books);

        // show
        $request = $this->request('/api/tag/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->books);

        // edit book
        $time = new \DateTime();

        $book = $this->request('/api/book/'.$this->bookId, 'PUT', [], [
            'title' => 'title'.$time->getTimestamp(),
            'author' => 'surname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'sold' => false,
            'removed' => false,
            'releaseYear' => 2019,
            'added' => 859,
            'cond' => $this->conditionId,
            'tags' => [$id],
            'format' => $this->formatId
        ]);

        // show book
        $request = $this->request('/api/book/'.$book->id, 'GET');

        $this->assertEquals(1, count($request->tags));

        // delete
        $request = $this->request('/api/tag/'.$id, 'DELETE');

        $this->assertEquals('The tag was deleted successfully.', $request->msg);
    }
}
