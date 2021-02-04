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
        $request = $this->request('/api/v1/branch/', 'GET');

        $this->assertIsArray($request);

        $this->assertIsInt($request[0]->id);
        $this->assertIsString($request[0]->name);
        $this->assertTrue(isset($request[0]->steps));
        $this->assertIsString($request[0]->currency);

        $branch = $request[0];

        // edit
        $request = $this->request('/api/v1/branch/'.$branch->id, 'PUT', [], [
            'name' => 'name',
            'steps' => 0.01,
            'currency' => 'EUR',
        ]);

        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals(0.01, $request->steps);
        $this->assertEquals('EUR', $request->currency);

        // my
        $request = $this->request('/api/v1/branch/my', 'GET');

        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertTrue(isset($request->steps));
        $this->assertIsString($request->currency);

        // show
        $request = $this->request('/api/v1/branch/'.$branch->id, 'GET');

        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals(0.01, $request->steps);
        $this->assertEquals('EUR', $request->currency);
    }
}
