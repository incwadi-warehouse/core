<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreTest extends WebTestCase
{
    protected $client;

    public function setUp() {
        $this->getClient();
    }

    public function testScenario()
    {
        // index
        $request = $this->request('/', 'GET', [], []);

        $this->assertInternalType('array', $request);

        // index2
        $request = $this->request('/v1', 'GET', [], []);

        $this->assertInternalType('array', $request);

        // user
        $request = $this->request('/v1/me', 'GET', [], []);

        $this->assertInternalType('int', $request->id);
        $this->assertInternalType('string', $request->username);
        $this->assertInternalType('array', $request->roles);
        if ($request->branch !== null) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
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

    protected function getClient() {
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
