<?php

/*
 * This script is part of baldeweg/incwadi-core
 *
 * Copyright 2019 André Baldeweg <kontakt@andrebaldeweg.de>
 * MIT-licensed
 */

namespace Incwadi\Core\Tests\Command;

use Incwadi\Core\Command\NewUserCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class NewUserCommandTest extends TestCase
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
        $application->add(new NewUserCommand($em, $encoder));
        $command = $application->find('user:new');

        $this->assertEquals(
            'user:new',
            $command->getName(),
            'NewUserCommandTest user:new'
        );
    }
}
