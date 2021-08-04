<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatsTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        $request = $this->request('/api/stats/', 'GET', [], []);

        $this->assertIsInt($request->all);
        $this->assertIsInt($request->available);
        $this->assertIsInt($request->reserved);
        $this->assertIsInt($request->sold);
        $this->assertIsInt($request->removed);
    }
}
