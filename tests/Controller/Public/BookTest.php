<?php

namespace Incwadi\Core\Tests\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    private int $branch;

    public function setUp(): void
    {
        $this->buildClient();

        $request = $this->request('/api/branch/my', 'GET');

        $this->branch = $request->id;
    }

    public function testScenario()
    {
        // find
        $request = $this->request('/api/public/book/find', 'GET', [
            'options' => json_encode(['term' => 'book', 'filter' => []]),
        ]);

        $this->assertIsArray($request->books);
        $this->assertEquals(13, count((array) $request->books[0]));
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
        if (null !== $request->books[0]->cond) {
            $this->assertIsString($request->books[0]->cond);
        }

        // recommendation
        $request = $this->request('/api/public/book/recommendation/'.$this->branch, 'GET');

        $this->assertIsArray($request->books);
        $this->assertIsInt($request->counter);
    }
}
