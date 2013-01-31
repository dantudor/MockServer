<?php

namespace MockServer\Server;

use JMS\Serializer\Serializer;
use Symfony\Component\Finder\Finder;

/**
 * Primer
 */
class Primer
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $primingDir;

    /**
     * Constructor
     *
     * @param int        $baseDir    Base Directory
     * @param Serializer $serializer Serializer
     */
    public function __construct($baseDir, Serializer $serializer)
    {
        $this->baseDir = realpath($baseDir. '/../');
        $this->serializer = $serializer;
    }

    /**
     * @param string $environment Environment
     * @param string $path        Path
     * @param string $method      Method
     * @param array  $data        Data
     */
    public function prime($environment, $path, $method, $data = null)
    {
        $this->initializePrimingDirectory($environment, $path, $method);

        $path = $this->primingDir . '/' . preg_replace('/[^\da-z]/i', '', microtime());

        file_put_contents($path, serialize($data));
    }

    /**
     * Initialize priming directory
     *
     * @param string $environment Environment
     * @param string $path        Path
     * @param string $method      Method
     *
     * @return Primer
     */
    protected function initializePrimingDirectory($environment, $path, $method)
    {
        $this->primingDir = $this->baseDir . '/' . $environment . '/mock/' . preg_replace('/[^\da-z]/i', '-', trim($path, '/')) . '/' . strtolower($method);

        if (false == file_exists($this->primingDir)) {
            mkdir($this->primingDir, 0777, true);
        }

        return $this;
    }

    /**
     * @param string $environment Environment
     * @param string $path        Path
     * @param string $method      Method
     *
     * @return string
     */
    public function getData($environment, $path, $method)
    {
        $this->initializePrimingDirectory($environment, $path, $method);

        $finder = new Finder();
        $finder->files()->in($this->primingDir)->sortByName();

        foreach ($finder as $file) {
            $data = unserialize(file_get_contents($file->getRealPath()));
            unlink($file->getRealPath());

            return $data;
        }
    }
}
