<?php

use MockFs\MockFs;

class ProcessManagerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
//        $this->markTestSkipped('A little screwed');
    }

    public function testConstructWithNoPidFilePresent()
    {
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

        $processes = new stdClass();
        $processes->{$process->pid} = $process;

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->reset();
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

        $this->assertEquals('{"12345":{"pid":"12345","host":"127.0.0.1","port":8080}}', $mockFs->getFileSystem()->getChild('MockServer.pids')->getContents());
    }

    public function testFlushEmptiesPidFile()
    {
        $process = new stdClass();
        $process->pid = 12345;
        $process->host  = '127.0.0.1';
        $process->port = 8080;

        $processes = new stdClass();
        $processes->{$process->pid} = $process;

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addFile('MockServer.pids', json_encode($processes));

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);
        $processManager->flush();

        $this->assertEquals(new stdClass(), $processManager->load());
    }
}
