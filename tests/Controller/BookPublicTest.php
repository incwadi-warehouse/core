<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookPublicTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    public function testScenario()
    {
        // find
        $request = $this->request('/api/public/book/find', 'GET', [
            'options' => json_encode(['term' => 'book']),
        ]);

        $this->assertIsArray($request);
        $this->assertEquals(16, count((array) $request[0]));
        $this->assertIsString($request[0]->id);
        $this->assertIsString($request[0]->currency);
        $this->assertIsString($request[0]->title);
        if (null !== $request[0]->shortDescription) {
            $this->assertTrue(isset($request[0]->shortDescription));
        }
        $this->assertIsString($request[0]->authorFirstname);
        $this->assertIsString($request[0]->authorSurname);
        $this->assertIsString($request[0]->genre);
        $this->assertNotEmpty($request[0]->price);
        $this->assertIsInt($request[0]->releaseYear);
        $this->assertIsString($request[0]->type);
        $this->assertIsString($request[0]->branchName);
        if (null !== $request[0]->branchOrdering) {
            $this->assertIsString($request[0]->branchOrdering);
        }
        if(null !== $request[0]->cover_s) {
            $this->assertIsString($request[0]->cover_s);
        }
        if(null !== $request[0]->cover_m) {
            $this->assertIsString($request[0]->cover_m);
        }
        if(null !== $request[0]->cover_l) {
            $this->assertIsString($request[0]->cover_l);
        }
        if (null !== $request[0]->cond) {
            $this->assertIsString($request[0]->cond);
        }
    }
}
