<?php

use MockServer\Server\ServerInterface;

class ServerInterfaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \MockServer\Server\ServerInterface
     */
    public function testServerInterfaceConstructor()
    {
        $port = 8080;
        $server = new TestServer($port);

        $this->assertInstanceOf('\MockServer\Server\ServerInterface', $server);
    }

    /**
     * @covers \MockServer\Server\ServerInterface::getPort
     */
    public function testServerInterfaceGetPort()
    {
        $port = 8080;
        $server = new TestServer($port);

        $this->assertSame($port, $server->getPort());
    }

    /**
     * @covers \MockServer\Server\ServerInterface::getHost
     */
    public function testServerInterfaceGetHost()
    {
        $host = 'mock.host';
        $server = new TestServer(0, $host);

        $this->assertSame($host, $server->getHost());
    }

    /**
     * @covers \MockServer\Server\ServerInterface::getSocket
     */

    public function testServerInterfaceGetSocket()
    {
        $socketType = '\React\Socket\Server';
        $server = new TestServer(8080);

        $this->assertInstanceOf($socketType, $server->getSocket());
    }

    /**
     * @covers \MockServer\Server\ServerInterface::getHttpServer
     */
    public function testServerInterfaceGetHttpServer()
    {
        $serverType = '\React\Http\Server';
        $server = new TestServer(8080);

        $this->assertInstanceOf($serverType, $server->getHttpServer());
    }

    /**
     * @covers \MockServer\Server\ServerInterface::start
     * @expectedException \MockServer\Exception\SocketConnectionException
     */
    public function testServerInterfaceStartThrowsExceptionWhenBindingFails()
    {
        $server = new TestServer(8080);

        // Stud the Event Loop
        $loop = $this->getMockBuilder('\React\EventLoop\LibEventLoop')->disableOriginalConstructor()->getMock();
        $server->setLoop($loop);

        // Stub the socket
        $socket = $this->getMockBuilder('\React\Socket\Server')->disableOriginalConstructor()->getMock();
        $server->setSocket($socket);

        $socket
            ->expects($this->once())
            ->method('listen')
            ->will($this->throwException(new \React\Socket\ConnectionException));

        $server->start();
    }

    /**
     * @covers \MockServer\Server\ServerInterface::start
     */
    public function testServerInterfaceStartSuccess()
    {
        $server = new TestServer(8080);

        // Stud the Event Loop
        $loop = $this->getMockBuilder('\React\EventLoop\LibEventLoop')->disableOriginalConstructor()->getMock();
        $server->setLoop($loop);

        // Stub the socket
        $socket = $this->getMockBuilder('\React\Socket\Server')->disableOriginalConstructor()->getMock();
        $server->setSocket($socket);

        $socket
            ->expects($this->once())
            ->method('listen')
            ->will($this->returnValue(true));

        $server->start();
    }
}

class TestServer extends ServerInterface
{
    public function getLoop()
    {
        return $this->loop;
    }

    public function setLoop($loop)
    {
        $this->loop = $loop;
    }

    public function setSocket($socket)
    {
        $this->socket = $socket;
    }

    public function onRequest(\React\Http\Request $request, \React\Http\Response $response) {

    }
}
