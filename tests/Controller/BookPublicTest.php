<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    public function testScenario()
    {
        // find
        $request = $this->request('/api/public/book/find', 'GET', [
            'options' => json_encode(['term' => 'book']),
        ]);

        $this->assertIsObject($request);
        $this->assertEquals(9, count($request));
        $this->assertInternalType('string', $request->id);
        $this->assertInternalType('string', $request->currency);
        $this->assertInternalType('string', $request->title);
        $this->assertInternalType('string', $request->authorFirstname);
        $this->assertInternalType('string', $request->authorLastname);
        $this->assertInternalType('string', $request->genre);
        $this->assertNotEmpty($request->price);
        $this->assertInternalType('int', $request->releaseYear);
        $this->assertInternalType('string', $request->type);
    }
}
