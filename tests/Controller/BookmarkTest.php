<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// @deprecated
class BookmarkTest extends WebTestCase
{
    use \Baldeweg\Bundle\ApiBundle\ApiTestTrait;

    public function testScenario()
    {
        // List
        $request = $this->request('/api/bookmark/');

        $this->assertIsArray($request);

        // New
        $request = $this->request('/api/bookmark/new', 'POST', [], [
            'url' => 'http://domain.tld',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertEquals('http://domain.tld', $request->url);
        $this->assertIsString($request->name);

        $id = $request->id;

        // Edit
        $request = $this->request('/api/bookmark/'.$id, 'PUT', [], [
            'url' => 'http://domain1.tld',
            'name' => 'Name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertEquals('http://domain1.tld', $request->url);
        $this->assertEquals('Name', $request->name);

        // Show
        $request = $this->request('/api/bookmark/'.$id);

        $this->assertTrue(isset($request->id));
        $this->assertEquals('http://domain1.tld', $request->url);
        $this->assertEquals('Name', $request->name);

        // Delete
        $request = $this->request('/api/bookmark/'.$id, 'DELETE');

        $this->assertEquals(
            'The bookmark was deleted successfully.',
            $request->msg
        );
    }
}
