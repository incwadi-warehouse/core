<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatsTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    public function testScenario()
    {
        $request = $this->request('/v1/stats/', 'GET', [], []);

        $this->assertInternalType('int', $request->all);
        $this->assertInternalType('int', $request->available);
        $this->assertInternalType('int', $request->sold);
        $this->assertInternalType('int', $request->removed);
    }
}
