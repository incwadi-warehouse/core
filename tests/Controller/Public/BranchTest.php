<?php

namespace App\Tests\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BranchTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // branch
        $request = $this->request('/api/public/branch/', 'GET');

        $this->assertIsArray($request->branches);
        $this->assertEquals(2, count((array) $request->branches[0]));
        $this->assertIsInt($request->branches[0]->id);
        $this->assertIsString($request->branches[0]->name);
    }
}
