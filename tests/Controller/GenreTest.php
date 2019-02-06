<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 */

namespace Baldeweg\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GenreTest extends WebTestCase
{
    public function testScenario()
    {
        // new
        $action = 'new';
        $request = $this->request($action, 'POST', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        $id = $request->id;

        // edit
        $action = $id;
        $request = $this->request($action, 'PUT', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        // list
        $action = '/';
        $request = $this->request($action, 'GET');

        $this->assertInternalType('array', $request);
    }

    protected function request(string $action, ?string $method = 'GET', ?array $params = [], ?array $content = [])
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password'
        ]);

        $crawler = $client->request(
            $method,
            '/genre/' . $action,
            $params,
            [],
            [],
            json_encode($content)
        );

        $this->assertTrue($client->getResponse()->isSuccessful(), 'Unexpected HTTP status code for ' . $method . ' /genre/' . $action);

        return json_decode($client->getResponse()->getContent());
    }
}
