<?php
namespace MockServer\Server;

use React\EventLoop\Factory as EventLoop;
use React\Socket\Server as Socket;
use React\Socket\ConnectionException;
use React\Http\Server as HttpServer;
use React\Http\Request;
use React\Http\Response;

use MockServer\Exception\SocketConnectionException;

abstract class ServerInterface
{
    /**
     * @var \React\EventLoop\LibEventLoop|\React\EventLoop\StreamSelectLoop
     */
    protected $loop;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var HttpServer
     */
    protected $httpServer;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($port, $host = '127.0.0.1')
    {
        $this->port = (int) $port;
        $this->host = (string) $host;

        $this->loop = EventLoop::create();
        $this->socket = new Socket($this->loop);
        $this->httpServer = new HttpServer($this->socket);

        $server = $this;
        $this->httpServer->on('request', function ($request, $response) use ($server) {
            // @codeCoverageIgnoreStart
            $server->onRequest($request, $response);
            // @codeCoverageIgnoreEnd
        });
    }

    /**
     * Get Server Port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get Server Host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get the Server Socket
     *
     * @return Socket
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * Get the Http Server
     *
     * @return HttpServer
     */
    public function getHttpServer()
    {
        return $this->httpServer;
    }

    /**
     * Start the server
     *
     * @return Server
     */
    public function start()
    {
        try {
            $this->socket->listen($this->port, $this->host);
        } catch(ConnectionException $e) {
            throw new SocketConnectionException($e->getMessage(), $e->getCode());
        }

        $this->loop->run();
    }

    /**
     * onRequest Callback
     *
     * @param \React\Http\Request $request
     * @param \React\Http\Response $response
     */
    abstract public function onRequest(Request $request, Response $response);

}
