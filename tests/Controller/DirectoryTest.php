<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DirectoryTest extends WebTestCase
{
    use \Baldeweg\Bundle\ApiBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/directory/', 'GET', [ 'dir'=> './' ]);

        $this->assertTrue(isset($request));

        // // new
        // $request = $this->request('/api/directory/new', 'POST', [], [
        //     // params
        // ]);

        // $this->assertTrue(isset($request));
        // // add asserts

        // $id = $request->id;

        // // edit
        // $request = $this->request('/api/directory/' . $id, 'PUT', [], [
        //     // params
        // ]);

        // $this->assertTrue(isset($request));
        // // add asserts

        // // show
        // $request = $this->request('/api/directory/' . $id, 'GET');

        // $this->assertTrue(isset($request));
        // // add asserts

        // // delete
        // $request = $this->request('/api/directory/' . $id, 'DELETE');

        // $this->assertEquals('DELETED', $request->msg);
    }
}
