<?php
namespace MockServer;

use React\EventLoop\Factory as EventLoop;
use React\Socket\Server as Socket;
use React\Http\Server as HttpServer;

class Server
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
     * @var int
     */
    protected $childPId;

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
        $pid = pcntl_fork();

        if ($pid < 0) {
            // failed to fork
            exit;
        } elseif (0 === $pid) {
            // child process initiates the server
            $this->socket->listen($this->port, $this->host);
            $this->loop->run();
            $this->loop->stop();
            exit(0);
        } else {
            // parent process tracks the child
            $this->childPId = $pid;
        }

        return $this;
    }

    public function __destruct()
    {
        if (null !== $this->childPId) {
            posix_kill($this->childPId, 9);
        }
    }
}
