<?php

use MockServer\Manager\ServerManager;

class ServerManagerTest extends PHPUnit_Framework_TestCase
{
    public function testSetPidFile()
    {
        $pidFile = '/mock.the.pid.file';
        $serverManager = new ServerManager();

        $this->assertSame($serverManager, $serverManager->setPidFile($pidFile));
        $this->assertSame($pidFile, $serverManager->getPidFile());
    }

    public function testCreateServerWithInvalidClassThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $serverManager = new ServerManager();
        $serverManager->create('\Invalid\Class', 0);
    }
}