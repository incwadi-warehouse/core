<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReportTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    public function testScenario()
    {
        // new
        $request = $this->request('/v1/report/new', 'POST', [], [
            'name' => 'name',
            'limitTo' => 50,
        ]);

        $this->assertInternalType('int', $request->id);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);
        $this->assertEquals('name', $request->name);

        $id = $request->id;

        // list
        $request = $this->request('/v1/report/', 'GET');

        $this->assertInternalType('array', $request);
        $this->assertInternalType('int', $request[0]->id);
        $this->assertInternalType('int', $request[0]->branch->id);
        if ($request[0]->branch) {
            $this->assertInternalType('string', $request[0]->branch->name);
        }
        $this->assertInternalType('string', $request[0]->name);

        // edit
        $request = $this->request('/v1/report/'.$id, 'PUT', [], [
            'name' => 'name',
            'searchTerm' => null,
            'limitTo' => 50,
            'sold' => false,
            'removed' => false,
            'olderThenXMonths' => null,
            'branches' => null,
            'genres' => null,
            'lendMoreThenXMonths' => null,
            'orderBy' => null,
            'releaseYear' => null,
            'type' => null,
        ]);

        $this->assertEquals($id, $request->id);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);
        $this->assertEquals('name', $request->name);
        $this->assertEquals(null, $request->searchTerm);
        $this->assertEquals(50, $request->limitTo);
        $this->assertFalse($request->sold);
        $this->assertFalse($request->removed);
        $this->assertEquals(null, $request->olderThenXMonths);
        $this->assertEquals(null, $request->branches);
        $this->assertEquals(null, $request->genres);
        $this->assertEquals(null, $request->lendMoreThenXMonths);
        $this->assertEquals(null, $request->orderBy);
        $this->assertEquals(null, $request->releaseYear);
        $this->assertEquals(null, $request->type);

        // show
        $request = $this->request('/v1/report/'.$id, 'GET');

        $this->assertEquals($id, $request->id);
        $this->assertInternalType('int', $request->branch->id);
        $this->assertInternalType('string', $request->branch->name);
        $this->assertEquals('name', $request->name);

        // delete
        $request = $this->request('/v1/report/'.$id, 'DELETE');

        $this->assertEquals('The report was deleted successfully.', $request->msg);
    }
}
