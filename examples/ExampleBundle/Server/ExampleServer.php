<?php

namespace ExampleBundle\Server;

use MockServer\Server\SymfonyServerInterface;

class ExampleServer extends SymfonyServerInterface
{
    protected $kernelClassName = '\ExampleBundle\app\AppKernel';
}
