<?php

use MockServer\Command\StartServerCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartServerCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \MockServer\Command\StartServerCommand::configure
     */
    public function testConfigure()
    {
        $name = 'mock:server:start';
        $description = 'Start a new Mock Server';
        $startServerCommand = new \MockServer\Command\StartServerCommand();

        $this->assertSame($name, $startServerCommand->getName());
        $this->assertSame($description, $startServerCommand->getDescription());

        $arguments = $startServerCommand->getDefinition()->getArguments();

        $this->assertSame('class', $arguments['class']->getName());
        $this->assertTrue($arguments['class']->isRequired());
        $this->AssertSame('What server type should be started?', $arguments['class']->getDescription());

        $this->assertSame('host', $arguments['host']->getName());
        $this->assertTrue($arguments['host']->isRequired());
        $this->AssertSame('What hostname should the server use?', $arguments['host']->getDescription());

        $this->assertSame('port', $arguments['port']->getName());
        $this->assertTrue($arguments['port']->isRequired());
        $this->AssertSame('What port should the server run on?', $arguments['port']->getDescription());

        $this->assertSame('pidFile', $arguments['pidFile']->getName());
        $this->assertTrue($arguments['pidFile']->isRequired());
        $this->AssertSame('Where do you want to store your pid files?', $arguments['pidFile']->getDescription());
    }
}

/**
 * Test command class required to test protected functions
 */
class testStartServerCommand extends StartServerCommand
{

}
