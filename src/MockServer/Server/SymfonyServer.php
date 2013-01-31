<?php
namespace MockServer\Server;

use MockServer\Exception\KernelInvalidException;
use MockServer\Exception\KernelMissingException;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Kernel;
use React\Http\Request;
use React\Http\Response;
use Monolog\Logger;

/**
 * Symfony Server
 */
class SymfonyServer extends ServerInterface
{
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
        require_once $kernelDir . '/bootstrap.php.cache';
        require_once $kernelDir . '/MockKernel.php';

        $this->kernel = new \MockKernel($environment, true);

        if (false === $this->kernel instanceof Kernel) {
            throw new KernelInvalidException("The '{$this->kernelClassName}' kernel must extend \\Symfony\\Component\\HttpKernel\\Kernel");
        }

        parent::__construct($port, $host);
    }

    /**
     * @return \Symfony\Component\HttpKernel\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
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
