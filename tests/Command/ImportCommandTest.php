<?php

namespace App\Tests\Command;

use App\Command\ImportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ImportCommandTest extends TestCase
{
    public function testExecute()
    {
        $import = $this->getMockBuilder('\\App\\Service\\Portability\\Import')
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new ImportCommand($import));
        $command = $application->find('incwadi:import');

        $this->assertEquals(
            'incwadi:import',
            $command->getName(),
            'ImportCommandTest incwadi:import'
        );
    }
}
