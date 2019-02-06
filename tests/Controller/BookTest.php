<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 */

namespace Baldeweg\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    public function testScenario()
    {
        // new
        $action = 'new';
        $request = $this->request($action, 'POST', [], [
            'title' => 'title',
            'author' => 'author',
            'genre' => 1,
            'price' => '1.00',
            'stocked' => true
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('title', $request->title);
        $this->assertEquals('author', $request->author);
        $this->assertEquals(1, $request->genre);
        $this->assertEquals('1.00', $request->price);
        $this->assertEquals('EUR', $request->currency);
        $this->assertTrue($request->stocked);

        $id = $request->id;

        // edit
        $action = $id;
        $request = $this->request($action, 'PUT', [], [
            'title' => 'book',
            'author' => 'authors',
            'genre' => 2,
            'price' => '2.00',
            'stocked' => true
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('book', $request->title);
        $this->assertEquals('authors', $request->author);
        $this->assertEquals(2, $request->genre);
        $this->assertEquals('2.00', $request->price);
        $this->assertEquals('EUR', $request->currency);
        $this->assertTrue($request->stocked);

        // show
        $action = $id;
        $request = $this->request($action, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertInternalType('integer', $request->added);
        $this->assertEquals('book', $request->title);
        $this->assertEquals('authors', $request->author);
        $this->assertEquals(2, $request->genre);
        $this->assertEquals('2.00', $request->price);
        $this->assertEquals('EUR', $request->currency);
        $this->assertTrue($request->stocked);

        // find
        $action = 'find';
        $request = $this->request($action, 'GET', [
            'term' => 'book',
            'offset' => '0'
        ]);

        $this->assertInternalType('array', $request);
        $this->assertTrue(isset($request[0]->id));
        $this->assertInternalType('integer', $request[0]->id);
        $this->assertInternalType('integer', $request[0]->added);
        $this->assertEquals('book', $request[0]->title);
        $this->assertEquals('authors', $request[0]->author);
        $this->assertEquals(2, $request[0]->genre);
        $this->assertEquals('2.00', $request[0]->price);
        $this->assertEquals('EUR', $request[0]->currency);
        $this->assertTrue($request[0]->stocked);

        // delete
        $action = $id;
        $request = $this->request($action, 'DELETE');

        $this->assertEquals('The book was successfully deleted.', $request->msg);
    }

    protected function request(string $action, ?string $method = 'GET', ?array $params = [], ?array $content = [])
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password'
        ]);

        $crawler = $client->request(
            $method,
            '/book/' . $action,
            $params,
            [],
            [],
            json_encode($content)
        );

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Unexpected HTTP status code for ' . $method . ' /book/' . $action);

        return json_decode($client->getResponse()->getContent());
    }
}
