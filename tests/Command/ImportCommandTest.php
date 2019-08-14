<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Command;

use Incwadi\Core\Command\ImportCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ImportCommandTest extends TestCase
{
    public function testExecute()
    {
        $import = $this->getMockBuilder('\\Incwadi\\Core\\Util\\Import')
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
