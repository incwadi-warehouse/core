<?php

/*
 * This script is part of incwadi/core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Command;

use Incwadi\Core\Command\ResetPasswordUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class ResetPasswordUserCommandTest extends TestCase
{
    public function testExecute()
    {
        $em = $this->getMockBuilder('\\Doctrine\\ORM\\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $encoder = $this->getMockBuilder('\\Symfony\\Component\\Security\\Core\\Encoder\\UserPasswordEncoderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $application = new Application();
        $application->add(new ResetPasswordUserCommand($em, $encoder));
        $command = $application->find('user:reset-password');

        $this->assertEquals(
            'user:reset-password',
            $command->getName(),
            'ResetPasswordUserCommandTest user:reset-password'
        );
    }
}
