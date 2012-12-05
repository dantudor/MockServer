<?php

use MockServer\Server;

class ServerInterfaceTest extends PHPUnit_Framework_TestCase
{
    public function testMockServerGetPort()
    {
        $port = 8080;
        $server = new TestServer($port);

        $this->assertSame($server->getPort(), $port);
    }

    public function testMockServerGetHost()
    {
        $host = 'mock.host';
        $server = new TestServer(0, $host);

        $this->assertSame($server->getHost(), $host);
    }

    public function testMockServerGetSocket()
    {
        $server = new TestServer(8080);

        $this->assertInstanceOf('\React\Socket\Server', $server->getSocket());
    }

    public function testMockServerGetHttpServer()
    {
        $server = new TestServer(8080);

        $this->assertInstanceOf('\React\Http\Server', $server->getHttpServer());
    }
}

class TestServer extends \MockServer\Server\ServerInterface {

}
