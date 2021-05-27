<?php

namespace Incwadi\Core\Tests\Command;

use Incwadi\Core\Command\ListBranchesCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ListBranchesCommandTest extends TestCase
{
    public function testExecute()
    {
        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new ListBranchesCommand($em));
        $command = $application->find('branch:list');

        $this->assertEquals(
            'branch:list',
            $command->getName(),
            'ListBranchesCommandTest branch:list'
        );
    }
}
