<?php

use MockFs\MockFs;

class ProcessManagerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('A little screwed');
    }

    public function testConstructWithNoPidFilePresent()
    {
        $this->markTestSkipped('touch testing requires php-5.4');
        new MockFs();

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);

        $this->assertSame($processFile, $processManager->getProcessFile());
    }

    public function testGetProcessFile()
    {
        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addFile('MockServer.pids', '');

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);

        $this->assertSame($processFile, $processManager->getProcessFile());
    }

    public function testLoadProcessFile()
    {
        $process = new stdClass();
        $process->pid = 12345;
        $process->host  = '127.0.0.1';
        $process->port = 8080;

        $processes = array(
            '12345' => $process
        );

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addFile('MockServer.pids', json_encode($processes));

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);

        $this->assertEquals($processes, $processManager->load());
    }

    public function testSaveProcessFile()
    {
        $mockFs = new MockFs();
        $mockFs->getFileSystem()->reset();
        $mockFs->getFileSystem()->addFile('MockServer.pids');

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);
        $processManager->add(12345, '127.0.0.1', 8080);
        $processManager->save();

        $this->assertEquals('{"pid":12345,"host":"127.0.0.1","port":8080}' . PHP_EOL, $mockFs->getFileSystem()->getChild('MockServer.pids'));
    }

    public function testFlushEmptiesPidFile()
    {
        $this->markTestSkipped();

        $process = new stdClass();
        $process->pid = 12345;
        $process->host  = '127.0.0.1';
        $process->port = 8080;

        $processes = array(
            '12345' => $process
        );

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addFile('MockServer.pids', json_encode($processes));

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);
        $processManager->flush();

        $this->assertEquals(array(), $processManager->load());
    }
}
