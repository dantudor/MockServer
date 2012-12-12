<?php
namespace MockServer\Server;

use MockServer\Exception\KernelInvalidException;
use MockServer\Exception\KernelMissingException;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpKernel\Kernel;
use React\Http\Request;
use React\Http\Response;
use Monolog\Logger;

abstract class SymfonyServerInterface extends ServerInterface
{
    /**
     * @var string
     */
    protected $kernelClassName;

    /**
     * @var \Symfony\Component\HttpKernel\Kernel
     */
    protected $kernel;

    public function __construct($port, $host = '127.0.0.1')
    {
        if (false === class_exists($this->kernelClassName)) {
            throw new KernelMissingException("The '{$this->kernelClassName}' kernel does not exist");
        }

        $kernelClassName = $this->kernelClassName;

        $this->kernel = new $kernelClassName('dev', true);

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
