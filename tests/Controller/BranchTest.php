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

        $this->assertInternalType('array', $request);

        $this->assertInternalType('integer', $request[0]->id);
        $this->assertInternalType('string', $request[0]->name);

        $branch = $request[0];

        // edit
        $request = $this->request('/v1/branch/'.$branch->id, 'PUT', [], [
            'name' => 'name',
        ]);

        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);

        // show
        $request = $this->request('/v1/branch/'.$branch->id, 'GET');

        $this->assertInternalType('integer', $request->id);
        $this->assertEquals('name', $request->name);
    }
}
