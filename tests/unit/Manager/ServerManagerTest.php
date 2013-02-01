<?php

use MockServer\Manager\ServerManager;
use MockServer\Server\Primer;

class ServerManagerTest extends PHPUnit_Framework_TestCase
{
    public function testSetPidFile()
    {
        $rootDir = '/mock/root/dir';
        $cacheDir = '/mock/root/dir/cache';
        $pidFile = 'mock.the.pid.file';

        $mockFs = new \MockFs\MockFs();
        $fileSystem = $mockFs->getFileSystem();

        $fileSystem->addFile($pidFile, null, $rootDir);


        $serverManager = new ServerManager(new Primer('mfs://' . $cacheDir), $rootDir, 'mfs://' . $cacheDir, $pidFile);

        $this->assertSame($serverManager, $serverManager->setPidFile($pidFile));
        $this->assertSame($pidFile, $serverManager->getPidFile());
    }
}