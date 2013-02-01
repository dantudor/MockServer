<?php
namespace MockServer\Server;

use MockServer\Exception\KernelInvalidException;
use MockServer\Exception\KernelMissingException;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Kernel;
use React\Http\Request;
use React\Http\Response;

/**
 * Symfony Server
 */
class SymfonyServer
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
     * @var \Symfony\Component\HttpKernel\Kernel
     */
    protected $kernel;

    /**
     * Constructor
     *
     * @param string $kernelDir   Kernel Directory
     * @param string $environment Environment
     * @param int    $port        Port
     * @param string $host        Host
     *
     * @throws KernelMissingException
     * @throws KernelInvalidException
     */
    public function __construct($kernelDir, $environment, $port, $host = '127.0.0.1')
    {
        $this->port = (int) $port;
        $this->host = (string) $host;

        require_once $kernelDir . '/bootstrap.php.cache';
        require_once $kernelDir . '/MockKernel.php';

        $this->kernel = new \MockKernel($environment, true);

        if (false === $this->kernel instanceof Kernel) {
            throw new KernelInvalidException("The '{$this->kernelClassName}' kernel must extend \\Symfony\\Component\\HttpKernel\\Kernel");
        }

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
     * @return \Symfony\Component\HttpKernel\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
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
     * @param Request  $request  Request
     * @param Response $response Response
     */
    public function onRequest(Request $request, Response $response)
    {
        $uri = 'http://' . $this->getHost() . ':' . $this->getPort() . $request->getPath();
        $kernelResponse = $this->kernel->handle(SymfonyRequest::create($uri));
        $response->writeHead($kernelResponse->getStatusCode(), array('Content-Type' => 'text/html'));
        $response->end($kernelResponse->getContent());
    }
}
