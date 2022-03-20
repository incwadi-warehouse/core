<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenreTest extends WebTestCase
{
    use \Baldeweg\Bundle\ApiBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/genre/', 'GET');

        $this->assertTrue(isset($request));

        // new
        $request = $this->request('/api/genre/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        $id = $request->id;

        // edit
        $request = $this->request('/api/genre/'.$id, 'PUT', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        // show
        $request = $this->request('/api/genre/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);

        // delete
        $request = $this->request('/api/genre/'.$id, 'DELETE');

        $this->assertEquals('The genre was deleted successfully.', $request->msg);
    }

    public function testDeleteGenreWithReferringBooks()
    {
        // new genre
        $request = $this->request('/api/genre/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));

        $genreId = $request->id;

        // new book
        $request = $this->request('/api/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $genreId,
            'price' => '1.00',
            'sold' => false,
            'releaseYear' => 2019,
            'added' => 2367,
        ]);

        $this->assertTrue(isset($request->id));

        $id = $request->id;

        // delete book
        $request = $this->request('/api/book/'.$id, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);

        // delete genre
        $this->request('/api/genre/'.$genreId, 'DELETE');
    }
}
