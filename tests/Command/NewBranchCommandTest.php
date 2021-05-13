<?php

namespace Incwadi\Core\Tests\Command;

use Incwadi\Core\Command\NewBranchCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class NewBranchCommandTest extends TestCase
{
    public function testExecute()
    {
        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new NewBranchCommand($em));
        $command = $application->find('branch:new');

        $this->assertEquals(
            'branch:new',
            $command->getName(),
            'NewBranchCommandTest branch:new'
        );
    }
}
