<?php

/*
 * This script is part of incwadi/core
 */

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreTest extends WebTestCase
{
    use \Incwadi\Core\Tests\ApiTestTrait;

    public function testScenario()
    {
        // user
        $request = $this->request('/api/v1/me', 'GET', [], []);

        $this->assertInternalType('int', $request->id);
        $this->assertInternalType('string', $request->username);
        $this->assertInternalType('array', $request->roles);
        if (null !== $request->branch) {
            $this->assertInternalType('int', $request->branch->id);
            $this->assertInternalType('string', $request->branch->name);
        }
        $this->assertTrue($request->isUser);
        $this->assertTrue($request->isAdmin);
        if (null !== $request->lastLogin) {
            $this->assertInstanceOf(
                \DateTime::class,
                new \DateTime($request->lastLogin)
            );
        }
    }
}
