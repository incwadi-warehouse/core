<?php

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SavedSearchTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // new
        $request = $this->request('/api/v1/savedsearch/new', 'POST', [], [
            'name' => 'name',
            'query' => ['term' => 'term', 'filter' => []],
        ]);

        $this->assertIsInt($request->id);
        $this->assertIsObject($request->branch);
        $this->assertEquals('name', $request->name);
        $this->assertIsObject($request->query);
        $this->assertEquals('term', $request->query->term);

        $id = $request->id;

        // list
        $request = $this->request('/api/v1/savedsearch/', 'GET');

        $this->assertIsArray($request);
        $this->assertIsInt($request[0]->id);
        $this->assertIsObject($request[0]->branch);
        $this->assertIsString($request[0]->name);
        $this->assertIsObject($request[0]->query);

        // edit
        $request = $this->request('/api/v1/savedsearch/'.$id, 'PUT', [], [
            'name' => 'name',
            'query' => ['term' => 'term', 'filter' => []],
        ]);

        $this->assertEquals($id, $request->id);
        $this->assertIsObject($request->branch);
        $this->assertEquals('name', $request->name);
        $this->assertIsObject($request->query);

        // show
        $request = $this->request('/api/v1/savedsearch/'.$id, 'GET');

        $this->assertEquals($id, $request->id);
        $this->assertIsObject($request->branch);
        $this->assertEquals('name', $request->name);
        $this->assertIsObject($request->query);

        // delete
        $request = $this->request('/api/v1/savedsearch/'.$id, 'DELETE');

        $this->assertEquals('The saved search was deleted successfully.', $request->msg);
    }
}
