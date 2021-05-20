<?php

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InventoryTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // list
        $request = $this->request('/api/inventory/', 'GET');

        $this->assertIsArray($request);

        // new
        $request = $this->request('/api/inventory/new', 'POST', [], []);

        $this->assertIsInt($request->id);
        $this->assertTrue(isset($request->branch));
        $this->assertIsInt($request->startedAt);
        $this->assertNull($request->endedAt);
        $this->assertIsInt($request->found);
        $this->assertIsInt($request->notFound);

        $id = $request->id;

        // edit
        $endedAt = new \DateTime();
        $request = $this->request('/api/inventory/'.$id, 'PUT', [], [
            'endedAt' => $endedAt->getTimestamp(),
        ]);

        $this->assertIsInt($request->id);
        $this->assertTrue(isset($request->branch));
        $this->assertIsInt($request->startedAt);
        $this->assertIsInt($request->endedAt);
        $this->assertIsInt($request->found);
        $this->assertIsInt($request->notFound);

        // show
        $request = $this->request('/api/inventory/'.$id, 'GET');

        $this->assertIsInt($request->id);
        $this->assertTrue(isset($request->branch));
        $this->assertIsInt($request->startedAt);
        $this->assertIsInt($request->endedAt);
        $this->assertIsInt($request->found);
        $this->assertIsInt($request->notFound);

        // delete
        $request = $this->request('/api/inventory/'.$id, 'DELETE');

        $this->assertEquals('The inventory was deleted successfully.', $request->msg);
    }
}
