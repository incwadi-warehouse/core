<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BranchTest extends WebTestCase
{
    protected $clientAdmin;

    public function setUp(): void
    {
        $this->buildClient();
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
        $request = $this->request('/branch/'.$id, 'PUT', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        // show
        $request = $this->request('/branch/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);
    }

    protected function request(string $url, ?string $method = 'GET', ?array $params = [], ?array $content = [])
    {
        $client = $this->clientAdmin;

        $crawler = $client->request(
            $method,
            '/v1'.$url,
            $params,
            [],
            [],
            json_encode($content)
        );

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Unexpected HTTP status code for '.$method.' '.$url.'!');

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
