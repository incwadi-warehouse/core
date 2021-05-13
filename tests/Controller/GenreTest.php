<?php

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenreTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/v1/genre/', 'GET');

        $this->assertTrue(isset($request));

        // new
        $request = $this->request('/api/v1/genre/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        $id = $request->id;

        // edit
        $request = $this->request('/api/v1/genre/'.$id, 'PUT', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        // show
        $request = $this->request('/api/v1/genre/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        // delete
        $request = $this->request('/api/v1/genre/'.$id, 'DELETE');

        $this->assertEquals('The genre was deleted successfully.', $request->msg);
    }

    public function testDeleteGenreWithReferringBooks()
    {
        // new genre
        $request = $this->request('/api/v1/genre/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));

        $genreId = $request->id;

        // new book
        $request = $this->request('/api/v1/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $genreId,
            'price' => '1.00',
            'sold' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'added' => 2367,
        ]);

        $this->assertTrue(isset($request->id));

        $id = $request->id;

        // delete genre
        $request = $this->request('/api/v1/genre/'.$genreId, 'DELETE');

        $this->assertEquals('The genre was deleted successfully.', $request->msg);

        // show book
        $request = $this->request('/api/v1/book/'.$id, 'GET');

        $this->assertEquals(null, $request->genre);

        // delete book
        $request = $this->request('/api/v1/book/'.$id, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
    }
}
