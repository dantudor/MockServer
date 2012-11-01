<?php

spl_autoload_register(function($class) {
    $classFile = __DIR__ . '/../../examples/' . str_replace('\\', '/', $class) . '.php';
    if (true === file_exists($classFile)) {
        include_once($classFile);
    }
});

use MockServer\SymfonyServer;
use Guzzle\Http\Client;

class SymfonyServerTest extends PHPUnit_Framework_TestCase
{
    public function testMockSymfonyServerStart()
    {
        $baseClass = '\Symfony\Component\HttpKernel\Kernel';
        $kernelClass = '\ExampleBundle\app\AppKernel';
        $server = new SymfonyServer(8080, '127.0.0.1', $kernelClass);

        $this->assertInstanceOf($baseClass, $server->getKernel());
        $this->assertInstanceOf($kernelClass, $server->getKernel());
        $this->assertSame($server->start(), $server);
        $server->stop();
    }

    public function testMockSymfonyServerWithInvalidKernelThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $kernelClass = '\Invalid\app\AppKernel';
        $server = new SymfonyServer(8080, '127.0.0.1', $kernelClass);

        $server->stop();
    }

    public function testMockSymfonyServerDefaultCallbackSuccess()
    {
        $port = 8080;
        $host = '127.0.0.1';
        $kernelClass = '\ExampleBundle\app\AppKernel';

        $server = new SymfonyServer($port, $host, $kernelClass);
        $server->start();

        $client = new Client('http://' . $host . ':' . $port);
        $request = $client->get('/');
        $response = $request->send();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame("<h1>I'm a Mock Server</h1>", $response->getBody(true));

        $server->stop();
    }

    public function testMockSymfonyServer404Success()
    {
        $port = 8080;
        $host = '127.0.0.1';
        $kernelClass = '\ExampleBundle\app\AppKernel';

        $server = new SymfonyServer($port, $host, $kernelClass);
        $server->start();

        $this->setExpectedException('Guzzle\Http\Exception\ClientErrorResponseException');
        $client = new Client('http://' . $host . ':' . $port);
        $request = $client->get('/invalid-route')->send();

        $server->stop();
    }
}