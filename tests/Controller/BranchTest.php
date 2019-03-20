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
    public function testScenario()
    {
        // list
        $request = $this->request('/branch/', 'GET');

        $this->assertInternalType('array', $request);

        $this->assertTrue(isset($request[0]->id));
        $this->assertInternalType('integer', $request[0]->id);
        $this->assertInternalType('string', $request[0]->name);

        $id = $request[0]->id;

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
