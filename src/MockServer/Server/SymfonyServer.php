<?php
namespace MockServer\Server;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use React\Http\Request;
use React\Http\Response;
use Symfony\Component\HttpKernel\Kernel;

class SymfonyServer extends InterfaceServer
{
    protected $kernelClassName;

    protected $kernel;

    public function __construct($port, $host)
    {
        if (false === class_exists($this->kernelClassName)) {
            throw new \InvalidArgumentException('The Kernel class does not exist: ' . $this->kernelClassName);
        }

        $kernelClassName = $this->kernelClassName;
        $this->kernel = new $kernelClassName('prod', false);

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
     * @param \React\Http\Request $request
     * @param \React\Http\Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
        $kernelResponse = $this->kernel->handle(SymfonyRequest::create($request->getPath()));

        $response->writeHead($kernelResponse->getStatusCode(), array('Content-Type' => 'text/html'));
        $response->end($kernelResponse->getContent());
    }
}