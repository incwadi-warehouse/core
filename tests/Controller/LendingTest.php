<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LendingTest extends WebTestCase
{
    protected $customerId;

    protected $bookId;

    protected $clientAdmin;


    public function setUp()
    {
        $this->buildClient();

        // new customer
        $request = $this->request('/customer/new', 'POST', [], [
            'name' => 'name',
            'notes' => 'notes'
        ]);

        $this->assertInternalType('int', $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);

        $this->customerId = $request->id;

        // new book
        $date = new \DateTime();
        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title ' . $date->getTimestamp(),
            'author' => 'surname,firstname',
            'genre' => null,
            'price' => '1.00',
            'stocked' => true,
            'releaseYear' => 2019,
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
        $this->assertInternalType('string', $request->title);
        $this->assertEquals('firstname', $request->author->firstname);
        $this->assertEquals('surname', $request->author->surname);
        $this->assertEquals(null, $request->genre);
        $this->assertEquals('1.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->releaseYear);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);

        $this->bookId = $request->id;
    }

    public function tearDown()
    {
        // delete customer
        $request = $this->request('/customer/' . $this->customerId, 'DELETE');

        $this->assertEquals('The customer was successfully deleted.', $request->msg);

        // delete book
        $request = $this->request('/book/' . $this->bookId, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
    }

    public function testScenario()
    {
        // list
        $request = $this->request('/lending/', 'GET', [], []);

        $this->assertInternalType('array', $request);

        // new
        $request = $this->request('/lending/new', 'POST', [], [
            'customer' => $this->customerId,
            'book' => $this->bookId
        ]);

        $this->assertInternalType('integer', $request->id);
        $this->assertTrue(isset($request->customer));
        $this->assertTrue(isset($request->book));
        $this->assertInternalType('string', $request->lendOn);

        $id = $request->id;

        // show
        $request = $this->request('/lending/' . $id, 'GET');

        $this->assertInternalType('integer', $request->id);
        $this->assertTrue(isset($request->customer));
        $this->assertTrue(isset($request->book));
        $this->assertInternalType('string', $request->lendOn);

        // delete
        $request = $this->request('/lending/' . $id, 'DELETE');

        $this->assertEquals('The lending was successfully deleted.', $request->msg);
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
