<?php
namespace MockServer\Server;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Kernel;
use React\Http\Request;
use React\Http\Response;
use Monolog\Logger;

abstract class SymfonyServerInterface extends ServerInterface
{
    protected $kernelClassName;

    protected $kernel;

    public function __construct($port, $host, Logger $logger = null)
    {
        if (false === class_exists($this->kernelClassName)) {
            throw new \InvalidArgumentException('The Kernel class does not exist: ' . $this->kernelClassName);
        }

        $kernelClassName = $this->kernelClassName;
        $this->kernel = new $kernelClassName('prod', false);

        parent::__construct($port, $host);

        //@codeCoverageIgnoreOn
        if (null !== $logger) {
            $this->logger = $logger;
            $this->logger->info('Created Server: ', array('host' => $host, 'port' => $port));
        }
        //@codeCoverageIgnoreOff
    }

    /**
     * @return \Symfony\Component\HttpKernel\Kernel
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param \React\Http\Request $request
     * @param \React\Http\Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
        //@codeCoverageIgnoreOn
        if (null !== $this->logger) {
            $this->logger->info('Request: ', array('path' => $request->getPath(), 'method' => $request->getMethod(), 'query' => $request->getQuery()));
        }
        //@codeCoverageIgnoreOff

        $kernelResponse = $this->kernel->handle(SymfonyRequest::create($request->getPath()));

        $response->writeHead($kernelResponse->getStatusCode(), array('Content-Type' => 'text/html'));
        $response->end($kernelResponse->getContent());
    }
}
