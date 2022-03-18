<?php

namespace App\Tests\Command;

use App\Command\ListBranchesCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ListBranchesCommandTest extends TestCase
{
    public function testExecute()
    {
        $repo = $this->getMockBuilder('\\App\\Repository\\BranchRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new ListBranchesCommand($repo));
        $command = $application->find('branch:list');

        $this->assertEquals(
            'branch:list',
            $command->getName(),
            'ListBranchesCommandTest branch:list'
        );
    }
}
