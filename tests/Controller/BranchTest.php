<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BranchTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/v1/branch/', 'GET');

        $this->assertInternalType('array', $request->branches);

        $this->assertTrue(isset($request->branches[0]->id));
        $this->assertInternalType('integer', $request->branches[0]->id);
        $this->assertInternalType('string', $request->branches[0]->name);

        $id = $request->branches[0]->id;

        // edit
        $request = $this->request('/v1/branch/'.$id, 'PUT', [], [
            'name' => 'name'
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        // show
        $request = $this->request('/v1/branch/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);
    }
}
