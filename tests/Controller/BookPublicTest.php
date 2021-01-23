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
        $this->assertEquals(9, count((array)$request->books[0]));
        $this->assertInternalType('string', $request->books[0]->id);
        $this->assertInternalType('string', $request->books[0]->currency);
        $this->assertInternalType('string', $request->books[0]->title);
        $this->assertInternalType('string', $request->books[0]->authorFirstname);
        $this->assertInternalType('string', $request->books[0]->authorSurname);
        $this->assertInternalType('string', $request->books[0]->genre);
        $this->assertNotEmpty($request->books[0]->price);
        $this->assertInternalType('int', $request->books[0]->releaseYear);
        $this->assertInternalType('string', $request->books[0]->type);
    }
}
