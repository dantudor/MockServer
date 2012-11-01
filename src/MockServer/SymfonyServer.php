<?php
namespace MockServer;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class SymfonyServer extends Server
{
    protected $kernel;

    public function __construct($port, $host, $kernel)
    {
        if (false === class_exists($kernel)) {
            throw new \InvalidArgumentException('The Kernel class does not exist');
        }

        parent::__construct($port, $host);
        $this->kernel = new $kernel('prod', false);
    }

    /**
     * @return mixed
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param \React\Http\Request $request
     * @param \React\Http\Response $response
     */
    public function onRequest($request, $response)
    {
        $kernelResponse = $this->kernel->handle(SymfonyRequest::create($request->getPath()));

        $response->writeHead($kernelResponse->getStatusCode(), array('Content-Type' => 'text/html'));
        $response->end($kernelResponse->getContent());
    }
}