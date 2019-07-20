<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Command;

use Incwadi\Core\Command\DeleteUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class DeleteUserCommandTest extends TestCase
{
    public function testExecute()
    {
        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new DeleteUserCommand($em));
        $command = $application->find('user:delete');

        $this->assertEquals(
            'user:delete',
            $command->getName(),
            'DeleteUserCommandTest user:delete'
        );
    }
}
