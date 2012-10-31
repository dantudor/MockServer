<?php

use MockServer\Server;

class ServerTest extends PHPUnit_Framework_TestCase
{
    public function testMockServerGetPort()
    {
        $port = 8080;
        $server = new Server($port);

        $this->assertSame($server->getPort(), $port);
    }

    public function testMockServerGetHost()
    {
        $host = 'mock.host';
        $server = new Server(0, $host);

        $this->assertSame($server->getHost(), $host);
    }

    public function testMockServerGetSocket()
    {
        $server = new Server(8080);

        $this->assertInstanceOf('\React\Socket\Server', $server->getSocket());
    }

    public function testMockServerGetHttpServer()
    {
        $server = new Server(8080);

        $this->assertInstanceOf('\React\Http\Server', $server->getHttpServer());
    }

    public function testMockServerStart()
    {
        $server = new Server(8080);

        $this->assertSame($server, $server->start());
    }
}
