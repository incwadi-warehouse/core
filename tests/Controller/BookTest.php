<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    protected $genreId;


    public function setUp()
    {
        $request = $this->request('/genre/new', 'POST', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        $this->genreId = $request->id;
    }

    public function tearDown()
    {
        $request = $this->request('/genre/' . $this->genreId, 'DELETE');

        $this->assertEquals('The genre was successfully deleted.', $request->msg);
    }

    public function testScenario()
    {
        // index
        $request = $this->request('/book/', 'GET', [], []);

        $this->assertEquals([], $request);

        // new
        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'author',
            'genre' => $this->genreId,
            'price' => '1.00',
            'stocked' => true,
            'yearOfPublication' => 2019,
            'type' => 'paperback',
            'premium' => false
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        if ($request->branch) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('title', $request->title);
        $this->assertEquals('author', $request->author);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('1.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->yearOfPublication);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);
        $this->assertFalse($request->lending);

        $id = $request->id;

        // edit
        $request = $this->request('/book/' . $id, 'PUT', [], [
            'title' => 'book',
            'author' => 'authors',
            'genre' => $this->genreId,
            'price' => '2.00',
            'stocked' => true,
            'yearOfPublication' => 2019,
            'type' => 'paperback',
            'premium' => false
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        if ($request->branch) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('book', $request->title);
        $this->assertEquals('authors', $request->author);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->yearOfPublication);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);
        $this->assertFalse($request->lending);

        // show
        $request = $this->request('/book/' . $id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        if ($request->branch) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('book', $request->title);
        $this->assertEquals('authors', $request->author);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->yearOfPublication);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);
        $this->assertFalse($request->lending);

        // find
        $request = $this->request('/book/find', 'GET', [
            'term' => 'book',
            'offset' => '0'
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
        $this->assertEquals('book', $request->books[0]->title);
        $this->assertEquals('authors', $request->books[0]->author);
        if ($request->books[0]->genre) {
            $this->assertInternalType('integer', $request->books[0]->genre->id);
            $this->assertEquals('name', $request->books[0]->genre->name);
        }
        $this->assertEquals('2.00', $request->books[0]->price);
        $this->assertTrue($request->books[0]->stocked);
        $this->assertEquals(2019, $request->books[0]->yearOfPublication);
        $this->assertEquals('paperback', $request->books[0]->type);
        $this->assertFalse($request->books[0]->premium);
        $this->assertFalse($request->books[0]->lending);

        // delete
        $request = $this->request('/book/' . $id, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
    }

    public function testDuplicate() {
        // new
        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'author',
            'genre' => $this->genreId,
            'price' => '1.00',
            'stocked' => true,
            'yearOfPublication' => 2019,
            'type' => 'paperback',
            'premium' => false
        ]);

        $this->assertTrue(isset($request->id));

        // new
        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'author',
            'genre' => $this->genreId,
            'price' => '1.00',
            'stocked' => true,
            'yearOfPublication' => 2019,
            'type' => 'paperback',
            'premium' => false
        ]);

        $this->assertEquals('Book not saved, because it exists already!', $request->msg);
    }

    protected function request(string $url, ?string $method = 'GET', ?array $params = [], ?array $content = [])
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password'
        ]);

        $crawler = $client->request(
            $method,
            '/v1' . $url,
            $params,
            [],
            [],
            json_encode($content)
        );

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Unexpected HTTP status code for ' . $method . ' ' . $url . '!');

        return json_decode($client->getResponse()->getContent());
    }
}
