<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerTest extends WebTestCase
{
    public function testScenario()
    {
        // list
        $request = $this->request('/customer/', 'GET');

        $this->assertInternalType('array', $request);
        $this->assertInternalType('int', $request[0]->id);
        $this->assertInternalType('string', $request[0]->name);
        if ($request[0]->notes) {
            $this->assertInternalType('string', $request[0]->notes);
        }
        $this->assertInternalType('array', $request[0]->lends);

        // new
        $request = $this->request('/customer/new', 'POST', [], [
            'name' => 'name',
            'notes' => 'notes'
        ]);

        $this->assertInternalType('int', $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);
        $this->assertInternalType('array', $request->lends);

        $id = $request->id;

        // edit
        $request = $this->request('/customer/' . $id, 'PUT', [], [
            'name' => 'name',
            'notes' => 'notes'
        ]);

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);
        $this->assertInternalType('array', $request->lends);

        // show
        $request = $this->request('/customer/' . $id, 'GET');

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);
        $this->assertInternalType('array', $request->lends);

        // delete
        $request = $this->request('/customer/' . $id, 'DELETE');

        $this->assertEquals('The customer was successfully deleted.', $request->msg);
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
