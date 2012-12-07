<?php

namespace {{ namespace }}\Server;

use MockServer\Server\SymfonyServerInterface;

class {{ bundle_basename }}Server extends SymfonyServerInterface
{
    protected $kernelClassName = '\{{ namespace }}\app\AppKernel';
}
