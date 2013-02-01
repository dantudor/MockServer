<?php

namespace MockServer\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Mock Generator
 */
class MockGenerator extends Generator
{
    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /** @var string */
    private $deployDir;

    /**
     * @param Filesystem $filesystem File System
     * @param string     $deployDir  Deploy Directory
     */
    public function __construct(Filesystem $filesystem, $deployDir)
    {
        $this->filesystem = $filesystem;
        $this->deployDir = $deployDir;
    }

    /**
     * Generate
     *
     * @param string $rootDir Root Directory
     * @param string $name    Mock Server Name
     *
     * @throws \RuntimeException
     */
    public function generate($rootDir, $name)
    {
        if (true === $this->filesystem->exists($rootDir . '/mock/' . $name)) {
            throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" already exists.', realpath($rootDir . '/mock/' . $name)));
        }

        $this->filesystem->mkdir($rootDir . '/mock/' . $name);
        $parameters = array(
            'name' => $name,
        );

        $this->renderFile($this->deployDir, 'config.yml', $rootDir . '/mock/' . $name . '/config.yml', $parameters);
        $this->renderFile($this->deployDir, 'parameters.yml', $rootDir . '/mock/' . $name . '/parameters.yml', $parameters);
        $this->renderFile($this->deployDir, 'routing.yml', $rootDir . '/mock/' . $name . '/routing.yml', $parameters);
        $this->renderFile($this->deployDir, 'security.yml', $rootDir . '/mock/' . $name . '/security.yml', $parameters);
    }
}
