<?php

namespace Incwadi\Core\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreTest extends WebTestCase
{
    use \Baldeweg\Bundle\ExtraBundle\ApiTestTrait;

    public function testScenario()
    {
        // user
        $request = $this->request('/api/me', 'GET', [], []);

        $this->assertIsInt($request->id);
        $this->assertIsString($request->username);
        $this->assertIsArray($request->roles);
        if (null !== $request->branch) {
            $this->assertIsInt($request->branch->id);
            $this->assertIsString($request->branch->name);
        }
        $this->assertTrue($request->isUser);
        $this->assertTrue($request->isAdmin);
    }
}
