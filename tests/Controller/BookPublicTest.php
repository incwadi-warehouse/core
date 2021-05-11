<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookPublicTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    private int $branch;

    public function setUp(): void
    {
        $this->buildClient();

        $request = $this->request('/api/v1/branch/my', 'GET');

        $this->branch = $request->id;
    }

    public function testScenario()
    {
        // find
        $request = $this->request('/api/public/book/find', 'GET', [
            'options' => json_encode(['term' => 'book', 'filter' => []]),
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

        // branch
        $request = $this->request('/api/public/book/branch', 'GET');

        $this->assertIsArray($request->branches);
        $this->assertEquals(2, count((array) $request->branches[0]));
        $this->assertIsInt($request->branches[0]->id);
        $this->assertIsString($request->branches[0]->name);

        // recommendation
        $request = $this->request('/api/public/book/recommendation/'.$this->branch, 'GET');

        $this->assertIsArray($request->books);
        $this->assertIsInt($request->counter);
    }
}
