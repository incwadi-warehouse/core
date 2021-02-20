<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatsTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        $request = $this->request('/api/v1/stats/', 'GET', [], []);

        $this->assertIsInt($request->all);
        $this->assertIsInt($request->available);
        $this->assertIsInt($request->sold);
        $this->assertIsInt($request->removed);
    }
}
