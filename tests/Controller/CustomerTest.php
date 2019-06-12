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
    protected $clientAdmin;

    public function setUp()
    {
        $this->buildClient();
    }

    public function testScenario()
    {
        // new
        $request = $this->request('/customer/new', 'POST', [], [
            'name' => 'name',
            'notes' => 'notes'
        ]);

        $this->assertInternalType('int', $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);
        $this->assertTrue(isset($request->books));
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        $id = $request->id;

        // list
        $request = $this->request('/customer/', 'GET');

        $this->assertInternalType('array', $request->customers);
        $this->assertInternalType('int', $request->customers[0]->id);
        $this->assertInternalType('string', $request->customers[0]->name);
        if ($request->customers[0]->notes) {
            $this->assertInternalType('string', $request->customers[0]->notes);
        }
        $this->assertTrue(isset($request->customers[0]->books));
        if ($request->customers[0]->branch) {
            $this->assertInternalType('int', $request->customers[0]->branch->id);
            $this->assertInternalType('string', $request->customers[0]->branch->name);
        }

        // edit
        $request = $this->request('/customer/' . $id, 'PUT', [], [
            'name' => 'name',
            'notes' => 'notes'
        ]);

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);
        $this->assertTrue(isset($request->books));
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        // show
        $request = $this->request('/customer/' . $id, 'GET');

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals('notes', $request->notes);
        $this->assertTrue(isset($request->books));
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        // delete
        $request = $this->request('/customer/' . $id, 'DELETE');

        $this->assertEquals('The customer was successfully deleted.', $request->msg);
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
