<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BranchTest extends WebTestCase
{
    protected $client;

    public function setUp()
    {
        $this->getClient();
    }

    public function testScenario()
    {
        // list
        $request = $this->request('/branch/', 'GET');

        $this->assertInternalType('array', $request->branches);

        $this->assertTrue(isset($request->branches[0]->id));
        $this->assertInternalType('integer', $request->branches[0]->id);
        $this->assertInternalType('string', $request->branches[0]->name);

        $id = $request->branches[0]->id;

        // edit
        $request = $this->request('/branch/' . $id, 'PUT', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        // show
        $request = $this->request('/branch/' . $id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);
    }

    protected function request(string $url, ?string $method = 'GET', ?array $params = [], ?array $content = [])
    {
        $client = $this->client;

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

    protected function getClient()
    {
        $this->client = static::createClient();
        $this->client->request(
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
            $this->client->getResponse()->getContent(),
            true
        );
        $this->client->setServerParameter(
            'HTTP_Authorization',
            sprintf('Bearer %s', $data['token'])
        );
    }
}
