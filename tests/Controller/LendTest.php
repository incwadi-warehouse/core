<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LendTest extends WebTestCase
{
    protected $customerId;

    protected $bookId;


    public function setUp()
    {
        // new customer
        $request = $this->request('/customer/new', 'POST', [], [
            'name' => 'name',
            'notes' => 'notes'
        ]);

        $this->assertInternalType('int', $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);
        $this->assertInternalType('int', $request->lends);

        $this->customerId = $request->id;

        // new book
        $request = $this->request('/book/new', 'POST', [], [
            'title' => 'title',
            'author' => 'author',
            'genre' => null,
            'price' => '1.00',
            'stocked' => true,
            'yearOfPublication' => 2019,
            'type' => 'paperback',
            'premium' => false
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertNull($request->branch);
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('title', $request->title);
        $this->assertEquals('author', $request->author);
        $this->assertEquals(null, $request->genre);
        $this->assertEquals('1.00', $request->price);
        $this->assertTrue($request->stocked);
        $this->assertEquals(2019, $request->yearOfPublication);
        $this->assertEquals('paperback', $request->type);
        $this->assertFalse($request->premium);
        $this->assertFalse($request->lend);

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
        $request = $this->request('/lend/', 'GET', [], []);

        $this->assertInternalType('array', $request);

        // new
        $request = $this->request('/lend/new', 'POST', [], [
            'customer' => $this->customerId,
            'book' => $this->bookId
        ]);

        $this->assertInternalType('integer', $request->id);
        $this->assertTrue(isset($request->customer));
        $this->assertTrue(isset($request->book));
        $this->assertInternalType('string', $request->lendOn);

        $id = $request->id;

        // show
        $request = $this->request('/lend/' . $id, 'GET');

        $this->assertInternalType('integer', $request->id);
        $this->assertTrue(isset($request->customer));
        $this->assertTrue(isset($request->book));
        $this->assertInternalType('string', $request->lendOn);

        // delete
        $request = $this->request('/lend/' . $id, 'DELETE');

        $this->assertEquals('The lending was successfully deleted.', $request->msg);
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
