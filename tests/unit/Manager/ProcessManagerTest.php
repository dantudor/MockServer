<?php

use MockFs\MockFs;
use MockServer\Process\ProcessIteratorAggregate;
use MockServer\Process\Process;

class ProcessManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \MockServer\Manager\ProcessManager
     */
    public function testProcessManagerConstructWithNoPidFilePresent()
    {
        new MockFs();

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);

        $this->assertSame($processFile, $processManager->getProcessFile());
    }

    /**
     * @covers \MockServer\Manager\ProcessManager::getProcessFile
     */
    public function testProcessManagerGetProcessFile()
    {
        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addFile('MockServer.pids', '');

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);

        $this->assertSame($processFile, $processManager->getProcessFile());
    }

    /**
     * @covers \MockServer\Manager\ProcessManager::add
     */
    public function testProcessManagerAdd()
    {
        new MockFs();

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);

        $this->assertSame($processManager, $processManager->add('12345', 'mock.host', 999));

        $process = $processManager->save()->load()->getById(12345);

        $this->assertSame(12345, $process->getId());
        $this->assertSame('mock.host', $process->getHost());
        $this->assertSame(999, $process->getPort());
    }

    /**
     * @covers \MockServer\Manager\ProcessManager::load
     */
    public function testProcessManagerLoad()
    {
        $process = new Process(12345, '127.0.0.1', 8080);

        $processes = new ProcessIteratorAggregate();
        $processes->add($process);

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->reset();
        $mockFs->getFileSystem()->addFile('MockServer.pids', serialize($processes));

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);

        $this->assertEquals($processes, $processManager->load());
    }

    /**
     * @covers \MockServer\Manager\ProcessManager::save
     */
    public function testProcessManagerSave()
    {
        $mockFs = new MockFs();
        $mockFs->getFileSystem()->reset();
        $mockFs->getFileSystem()->addFile('MockServer.pids');

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);
        $processManager->add(12345, '127.0.0.1', 8080);
        $processManager->save();

        $this->assertInstanceOf('\MockServer\Process\ProcessIteratorAggregate', unserialize($mockFs->getFileSystem()->getChild('MockServer.pids')->getContents()));
    }

    /**
     * @covers \MockServer\Manager\ProcessManager::flush
     * @covers \MockServer\Manager\ProcessManager::match
     */
    public function testProcessManagerFlush()
    {
        $process = new Process(12345, '127.0.0.1', 8080);

        $processes = new ProcessIteratorAggregate();
        $processes->add($process);

        $mockFs = new MockFs();
        $mockFs->getFileSystem()->addFile('MockServer.pids', serialize($processes));

        $processFile = 'mfs://MockServer.pids';
        $processManager = new \MockServer\Manager\ProcessManager($processFile);
        $processManager->flush();

        $this->assertEquals(new ProcessIteratorAggregate(), $processManager->load());
    }
}
