<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ApiTestTrait
{
    protected KernelBrowser $clientAdmin;

    public function setUp(): void
    {
        $this->buildClient();
    }

    protected function request(string $url, ?string $method = 'GET', ?array $params = [], ?array $content = [], int $statusCode = 200)
    {
        $client = $this->clientAdmin;

        $crawler = $client->request(
            $method,
            $url,
            $params,
            [],
            [],
            json_encode($content)
        );

        $this->assertEquals($statusCode, $client->getResponse()->getStatusCode(), 'Unexpected HTTP status code for '.$method.' '.$url.'!');

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
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(
                [
                    'username' => 'admin',
                    'password' => 'password',
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
