<?php

use MockServer\Server;

class ServerTest extends PHPUnit_Framework_TestCase
{
    public function testMockServerGetHost()
    {
        $host = 'mock.host';
        $server = new Server($host);
        $this->assertSame($server->getHost(), $host);
    }

    public function testMockServerGetPort()
    {
        $port = 8080;
        $server = new Server('mock.host', $port);
        $this->assertSame($server->getPort(), $port);
    }
}
