<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BranchTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/branch/', 'GET');

        $this->assertIsArray($request);

        $this->assertIsInt($request[0]->id);
        $this->assertIsString($request[0]->name);
        $this->assertTrue(isset($request[0]->steps));
        $this->assertIsString($request[0]->currency);
        if (null !== $request[0]->ordering) {
            $this->assertIsString($request[0]->ordering);
        }
        $this->assertIsString($request[0]->orderBy);
        $this->assertIsBool($request[0]->public);

        $branch = $request[0];

        // edit
        $request = $this->request('/api/branch/'.$branch->id, 'PUT', [], [
            'name' => 'name',
            'steps' => 0.01,
            'currency' => 'EUR',
            'ordering' => 'ordering',
            'orderBy' => 'name',
            'public' => true,
        ]);

        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals(0.01, $request->steps);
        $this->assertEquals('EUR', $request->currency);
        $this->assertIsString($request->ordering);
        $this->assertIsString($request->orderBy);
        $this->assertTrue($request->public);

        // my
        $request = $this->request('/api/branch/my', 'GET');

        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertTrue(isset($request->steps));
        $this->assertIsString($request->currency);
        $this->assertIsString($request->ordering);
        $this->assertIsString($request->orderBy);
        $this->assertTrue($request->public);

        // show
        $request = $this->request('/api/branch/'.$branch->id, 'GET');

        $this->assertIsInt($request->id);
        $this->assertEquals('name', $request->name);
        $this->assertEquals(0.01, $request->steps);
        $this->assertEquals('EUR', $request->currency);
        $this->assertIsString($request->ordering);
        $this->assertIsString($request->orderBy);
        $this->assertTrue($request->public);
    }
}
