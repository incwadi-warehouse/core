<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StaffTest extends WebTestCase
{
    protected $clientAdmin;

    public function setUp(): void
    {
        $this->buildClient();
    }

    public function testScenario()
    {
        // new
        $request = $this->request('/staff/new', 'POST', [], [
            'name' => 'name'
        ]);

        $this->assertInternalType('int', $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        $id = $request->id;

        // list
        $request = $this->request('/staff/', 'GET');

        $this->assertInternalType('array', $request->staff);
        $this->assertInternalType('int', $request->staff[0]->id);
        $this->assertInternalType('string', $request->staff[0]->name);
        if ($request->staff[0]->branch) {
            $this->assertInternalType('int', $request->staff[0]->branch->id);
            $this->assertInternalType('string', $request->staff[0]->branch->name);
        }

        // edit
        $request = $this->request('/staff/'.$id, 'PUT', [], [
            'name' => 'name'
        ]);

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        // show
        $request = $this->request('/staff/'.$id, 'GET');

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        // delete
        $request = $this->request('/staff/'.$id, 'DELETE');

        $this->assertEquals('The staff member was successfully deleted.', $request->msg);
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
