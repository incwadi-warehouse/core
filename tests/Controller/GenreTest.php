<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenreTest extends WebTestCase
{
    protected $clientAdmin;

    public function setUp()
    {
        $this->buildClient();
    }

    public function testScenario()
    {
        // list
        $request = $this->request('/genre/', 'GET');

        $this->assertInternalType('array', $request->genres);

        // new
        $request = $this->request('/genre/new', 'POST', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        $id = $request->id;

        // edit
        $request = $this->request('/genre/' . $id, 'PUT', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        // show
        $request = $this->request('/genre/' . $id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        // delete
        $request = $this->request('/genre/' . $id, 'DELETE');

        $this->assertEquals('The genre was successfully deleted.', $request->msg);
    }

    public function testDeleteGenreWithReferringBooks()
    {
        // new genre
        $request = $this->request('/genre/new', 'POST', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));

        $genreId = $request->id;

        // new book
        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'surname,firstname',
            'genre' => $genreId,
            'price' => '1.00',
            'sold' => false,
            'releaseYear' => 2019,
            'type' => 'paperback',
            'premium' => false
        ]);

        $this->assertTrue(isset($request->id));

        $id = $request->id;

        // delete genre
        $request = $this->request('/genre/' . $genreId, 'DELETE');

        $this->assertEquals('The genre was successfully deleted.', $request->msg);

        // show book
        $request = $this->request('/book/' . $id, 'GET');

        $this->assertEquals(null, $request->genre);

        // delete book
        $request = $this->request('/book/' . $id, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
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
