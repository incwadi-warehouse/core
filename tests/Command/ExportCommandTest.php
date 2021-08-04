<?php

namespace App\Tests\Command;

use App\Command\ExportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ExportCommandTest extends TestCase
{
    public function testExecute()
    {
        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $export = $this->getMockBuilder('\\App\\Service\\Portability\\Export')
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new ExportCommand($em, $export));
        $command = $application->find('incwadi:export');

        $this->assertEquals(
            'incwadi:export',
            $command->getName(),
            'ExportCommandTest incwadi:export'
        );
    }
}
