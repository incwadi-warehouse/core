<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConditionTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // new
        $request = $this->request('/api/condition/new', 'POST', [], [
            'name' => 'name',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsString($request->name);
        $this->assertIsInt($request->branch->id);

        $id = $request->id;

        // list
        $request = $this->request('/api/condition/', 'GET');

        $this->assertIsArray($request);

        $this->assertTrue(isset($request[0]->id));
        $this->assertIsString($request[0]->name);
        $this->assertIsInt($request[0]->branch->id);

        // show
        $request = $this->request('/api/condition/'.$id, 'GET');

        $this->assertTrue(isset($request->id));
        $this->assertIsString($request->name);
        $this->assertIsInt($request->branch->id);

        // edit
        $request = $this->request('/api/condition/'.$id, 'PUT', [], [
            'name' => 'name1',
        ]);

        $this->assertTrue(isset($request->id));
        $this->assertIsString($request->name);
        $this->assertIsInt($request->branch->id);

        // delete
        $request = $this->request('/api/condition/'.$id, 'DELETE');

        $this->assertEquals(
            'The condition was successfully deleted.',
            $request->msg
        );
    }
}
