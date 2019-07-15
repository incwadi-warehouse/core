<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Baldeweg\Tests\Command;

use Baldeweg\Command\ExportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ExportCommandTest extends TestCase
{
    public function testExecute()
    {
        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $export = $this->getMockBuilder('\\Baldeweg\\Util\\Export')
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
