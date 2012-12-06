<?php

use MockFs\MockFs;

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
        $processes = $processManager->save()->load();

        $this->assertSame('12345', $processes->{12345}->pid);
        $this->assertSame('mock.host', $processes->{12345}->host);
        $this->assertSame(999, $processes->{12345}->port);
    }

    /**
     * @covers \MockServer\Manager\ProcessManager::load
     */
    public function testProcessManagerLoad()
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

        $this->assertEquals('{"12345":{"pid":"12345","host":"127.0.0.1","port":8080}}', $mockFs->getFileSystem()->getChild('MockServer.pids')->getContents());
    }

    /**
     * @covers \MockServer\Manager\ProcessManager::flush
     * @covers \MockServer\Manager\ProcessManager::match
     */
    public function testProcessManagerFlush()
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
