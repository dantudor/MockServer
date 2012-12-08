<?php

use MockFs\MockFs;

class GenerateServerCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $exampleDir;

    /**
     * @var \MockFs\MockFs
     */
    protected $MockFs;

    /**
     * setup
     */
    public function setUp()
    {
        $this->rootDir = __DIR__ . '/../../../src/MockServer';
        $this->exampleDir = __DIR__ . '/../../../examples';
        $this->mockFs = new \MockFs\MockFs();
    }

    /**
     * @covers \MockServer\Command\GenerateServerCommand
     */
    public function testGenerateServer()
    {
        $namespace = 'Mock/TestBundle';

        exec($this->rootDir . "/bin/mockServer mock:bundle:generate --no-interaction --namespace {$namespace} --dir {$this->exampleDir}", $output);
    }

    public function tearDown()
    {
        if (file_exists($this->exampleDir . '/Mock')) {
            exec('rm -r ' . $this->exampleDir . '/Mock');
        }
    }
}
