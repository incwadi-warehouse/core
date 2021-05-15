<?php

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StaffTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // new
        $request = $this->request('/api/staff/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->branch->id);
        $this->assertIsString($request->branch->name);

        $id = $request->id;

        // list
        $request = $this->request('/api/staff/', 'GET');

        $this->assertIsArray($request);
        $this->assertIsInt($request[0]->id);
        $this->assertIsString($request[0]->name);
        if ($request[0]->branch) {
            $this->assertIsInt($request[0]->branch->id);
            $this->assertIsString($request[0]->branch->name);
        }

        // edit
        $request = $this->request('/api/staff/'.$id, 'PUT', [], [
            'name' => 'name',
        ]);

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->branch->id);
        $this->assertIsString($request->branch->name);

        // show
        $request = $this->request('/api/staff/'.$id, 'GET');

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertIsInt($request->branch->id);
        $this->assertIsString($request->branch->name);

        // delete
        $request = $this->request('/api/staff/'.$id, 'DELETE');

        $this->assertEquals(
            'The staff member was deleted successfully.',
            $request->msg
        );
    }
}
