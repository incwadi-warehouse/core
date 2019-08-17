<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorTest extends WebTestCase
{
    protected $clientAdmin;

    public function setUp()
    {
        $this->buildClient();
    }

    public function testScenario()
    {
        // find
        $request = $this->request('/author/find', 'GET', ['term' => 'name']);

        $this->assertInternalType('array', $request->authors);

        // new
        $request = $this->request('/author/new', 'POST', [], [
            'firstname' => 'Firstname',
            'surname' => 'Surname'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('Firstname', $request->firstname);
        $this->assertEquals('Surname', $request->surname);

        $id = $request->id;

        // edit
        $request = $this->request('/author/'.$id, 'PUT', [], [
            'firstname' => 'Firstname1',
            'surname' => 'Surname1'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('Firstname1', $request->firstname);
        $this->assertEquals('Surname1', $request->surname);

        // show
        $request = $this->request('/author/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('Firstname1', $request->firstname);
        $this->assertEquals('Surname1', $request->surname);

        // delete
        $request = $this->request('/author/'.$id, 'DELETE');

        $this->assertEquals('The author was successfully deleted.', $request->msg);
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
