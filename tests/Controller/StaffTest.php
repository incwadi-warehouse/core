<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StaffTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    public function testScenario()
    {
        // new
        $request = $this->request('/v1/staff/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertInternalType('int', $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        $id = $request->id;

        // list
        $request = $this->request('/v1/staff/', 'GET');

        $this->assertInternalType('array', $request);
        $this->assertInternalType('int', $request[0]->id);
        $this->assertInternalType('string', $request[0]->name);
        if ($request[0]->branch) {
            $this->assertInternalType('int', $request[0]->branch->id);
            $this->assertInternalType('string', $request[0]->branch->name);
        }

        // edit
        $request = $this->request('/v1/staff/'.$id, 'PUT', [], [
            'name' => 'name',
        ]);

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        // show
        $request = $this->request('/v1/staff/'.$id, 'GET');

        $this->assertEquals($id, $request->id);
        $this->assertEquals('name', $request->name);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);

        // delete
        $request = $this->request('/v1/staff/'.$id, 'DELETE');

        $this->assertEquals(
            'The staff member was deleted successfully.',
            $request->msg
        );
    }
}
