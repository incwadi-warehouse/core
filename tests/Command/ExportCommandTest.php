<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Command;

use Incwadi\Core\Command\ExportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ExportCommandTest extends TestCase
{
    public function testExecute()
    {
        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $export = $this->getMockBuilder('\\Incwadi\\Core\\Util\\Export')
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
