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

    protected $clientAdmin;


    public function setUp()
    {
        $this->buildClient();

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
            'author' => 'lastname,firstname',
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
        $this->assertEquals('firstname', $request->author->firstname);
        $this->assertEquals('lastname', $request->author->lastname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('1.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->yearOfPublication);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);

        $id = $request->id;

        // edit
        $request = $this->request('/book/' . $id, 'PUT', [], [
            'title' => 'book',
            'author' => 'lastname1,firstname1',
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
        $this->assertEquals('firstname1', $request->author->firstname);
        $this->assertEquals('lastname1', $request->author->lastname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->yearOfPublication);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);

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
        $this->assertEquals('firstname1', $request->author->firstname);
        $this->assertEquals('lastname1', $request->author->lastname);
        $this->assertEquals($this->genreId, $request->genre->id);
        $this->assertEquals('name', $request->genre->name);
        $this->assertEquals('2.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->yearOfPublication);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);
        $this->assertNull($request->lendTo);
        $this->assertNull($request->lendOn);

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
        $this->assertInternalType('string', $request->books[0]->author->firstname);
        $this->assertInternalType('string', $request->books[0]->author->lastname);
        if ($request->books[0]->genre) {
            $this->assertInternalType('integer', $request->books[0]->genre->id);
            $this->assertInternalType('string', $request->books[0]->genre->name);
        }
        $this->assertEquals('2.00', $request->books[0]->price);
        $this->assertTrue($request->books[0]->stocked);
        $this->assertEquals(2019, $request->books[0]->yearOfPublication);
        $this->assertEquals('paperback', $request->books[0]->type);
        $this->assertFalse($request->books[0]->premium);
        $this->assertNull($request->books[0]->lendTo);
        $this->assertNull($request->books[0]->lendOn);

        // delete
        $request = $this->request('/book/' . $id, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
    }

    public function testDuplicate()
    {
        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'lastname,firstname',
            'genre' => $this->genreId,
            'price' => '1.00',
            'stocked' => true,
            'yearOfPublication' => 2019,
            'type' => 'paperback',
            'premium' => false
        ]);

        $this->assertInternalType('int', $request->id);

        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'lastname,firstname',
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
        $client = $this->clientAdmin;

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

    protected function buildClient()
    {
        $this->clientAdmin = static::createClient();
        $this->clientAdmin->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            json_encode(
                [
                    'username' => 'admin',
                    'password' => 'password'
                ]
            )
        );
        $data = json_decode(
            $this->clientAdmin->getResponse()->getContent(),
            true
        );
        $this->clientAdmin->setServerParameter(
            'HTTP_Authorization',
            sprintf('Bearer %s', $data['token'])
        );
    }
}
