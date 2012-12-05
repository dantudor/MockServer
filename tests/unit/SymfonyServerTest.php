<?php

spl_autoload_register(function($class) {
    $classFile = __DIR__ . '/../../examples/' . str_replace('\\', '/', $class) . '.php';
    if (true === file_exists($classFile)) {
        include_once($classFile);
    }
});

use MockServer\Manager\ServerManager;
use MockServer\SymfonyServer;

use Guzzle\Http\Client;

class SymfonyServerTest extends PHPUnit_Framework_TestCase
{
    public function testMockSymfonyServerStart()
    {
        $port = 8080;
        $host = '127.0.0.1';
        $server = '\ExampleBundle\Server\ExampleServer';
        $flags = array('--dev');

        $serverManager = new ServerManager('/tmp/MockServerPid');
        $serverManager->create($server, $port, $host, $flags);
    }
}
