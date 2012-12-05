<?php

use MockServer\Server\SymfonyServerInterface;

class SymfonyServerInterfaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \MockServer\Server\SymfonyServerInterface
     * @expectedException \MockServer\Exception\KernelMissingException
     */
    public function testSymfonyServerThrowsExceptionWhenKernelClassIsMissing()
    {
        $port = 8080;
        new TestMissingkernelSymfonyServer($port);
    }

    /**
     * @covers \MockServer\Server\SymfonyServerInterface
     * @expectedException \MockServer\Exception\KernelInvalidException
     */
    public function testSymfonyServerThrowsExceptionWhenKernelClassIsInvalid()
    {
        $port = 8080;
        new TestInvalidkernelSymfonyServer($port);
    }

    /**
     * @covers \MockServer\Server\SymfonyServerInterface
     */
    public function testSymfonyServerSuccess()
    {
        $port = 8080;
        $server = new TestSymfonyServer($port);

        $this->assertInstanceOf('\Mockserver\Server\SymfonyServerInterface', $server);
    }

    /**
     * @covers \MockServer\Server\SymfonyServerInterface::getKernel
     */
    public function testSymfonyServerGetkernel()
    {
        $port = 8080;
        $server = new TestSymfonyServer($port);

        $this->assertInstanceOf('ValidKernel', $server->getKernel());
    }

    public function testSymfonyServerOnRequest()
    {
        $port = 8080;
        $server = new TestSymfonyServer($port);

        $mockRequest = $this->getMockBuilder('\React\Http\Request')->disableOriginalConstructor()->getMock();
        $mockRequest->expects($this->once())->method('getPath');
        $mockResponse = $this->getMockBuilder('\React\Http\Response')->disableOriginalConstructor()->getMock();
        $mockResponse->expects($this->once())->method('writeHead');
        $mockResponse->expects($this->once())->method('end');

        $server->onRequest($mockRequest, $mockResponse);

    }

}

/**
 * My kernel class does not exist
 */
class TestMissingKernelSymfonyServer extends SymfonyServerInterface
{
    protected $kernelClassName = 'MissingKernel';
}

/**
 * My kernel class exists but is not valid
 */
class TestInvalidKernelSymfonyServer extends SymfonyServerInterface
{
    protected $kernelClassName = 'InvalidKernel';
}

/**
 * I'm a working test class
 */
class TestSymfonyServer extends SymfonyServerInterface
{
    protected $kernelClassName = 'ValidKernel';
}

/**
 * I don't extend \Symfony\Component\HttpKernel\Kernel and therefore am invalid
 */
class InvalidKernel
{

}

/**
 * I extend \Symfony\Component\HttpKernel\Kernel and therefore am invalid
 */
class ValidKernel extends \Symfony\Component\HttpKernel\Kernel
{
    public function handle(Symfony\Component\HttpFoundation\Request $request, $type = 1, $catch = true)
    {
        return new Symfony\Component\HttpFoundation\Response('', 200);
    }

    public function registerBundles()
    {

    }

    public function registerContainerConfiguration(Symfony\Component\Config\Loader\LoaderInterface $loader)
    {

    }
}