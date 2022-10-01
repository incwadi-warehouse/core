<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnnouncementTest extends WebTestCase
{
    use \Baldeweg\Bundle\ApiBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/announcement/', 'GET');

        $this->assertTrue(isset($request));

        // new
        $request = $this->request('/api/announcement/new', 'POST', [], [
            'title' => 'title',
            'body' => 'body'
        ]);

        $this->assertTrue(isset($request));
        $this->assertEquals('title', $request->title);
        $this->assertEquals('body', $request->body);

        $id = $request->id;

        // edit
        $request = $this->request('/api/announcement/' . $id, 'PUT', [], [
            'title' => 'title2',
            'body' => 'body2'
        ]);

        $this->assertTrue(isset($request));
        $this->assertEquals('title2', $request->title);
        $this->assertEquals('body2', $request->body);

        // show
        $request = $this->request('/api/announcement/' . $id, 'GET');

        $this->assertTrue(isset($request));
        $this->assertEquals('title2', $request->title);
        $this->assertEquals('body2', $request->body);

        // delete
        $request = $this->request('/api/announcement/' . $id, 'DELETE');

        $this->assertEquals('DELETED', $request->msg);
    }
}
