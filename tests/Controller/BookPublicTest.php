<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookPublicTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // find
        $request = $this->request('/api/public/book/find', 'GET', [
            'options' => json_encode(['term' => 'book']),
        ]);

        $this->assertIsArray($request->books);
        $this->assertEquals(16, count((array) $request->books[0]));
        $this->assertIsString($request->books[0]->id);
        $this->assertIsString($request->books[0]->currency);
        $this->assertIsString($request->books[0]->title);
        if (null !== $request->books[0]->shortDescription) {
            $this->assertTrue(isset($request->books[0]->shortDescription));
        }
        $this->assertIsString($request->books[0]->authorFirstname);
        $this->assertIsString($request->books[0]->authorSurname);
        $this->assertIsString($request->books[0]->genre);
        $this->assertNotEmpty($request->books[0]->price);
        $this->assertIsInt($request->books[0]->releaseYear);
        $this->assertIsString($request->books[0]->type);
        $this->assertIsString($request->books[0]->branchName);
        if (null !== $request->books[0]->branchOrdering) {
            $this->assertIsString($request->books[0]->branchOrdering);
        }
        if (null !== $request->books[0]->cover_s) {
            $this->assertIsString($request->books[0]->cover_s);
        }
        if (null !== $request->books[0]->cover_m) {
            $this->assertIsString($request->books[0]->cover_m);
        }
        if (null !== $request->books[0]->cover_l) {
            $this->assertIsString($request->books[0]->cover_l);
        }
        if (null !== $request->books[0]->cond) {
            $this->assertIsString($request->books[0]->cond);
        }
    }
}
