<?php

namespace MockServer\Manager;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use MockServer\Server\Primer;

/**
 * Server Manager
 *
 * The server manager creates new server instances and reports them to the process manager
 * No doubt this will become much more comprehensive in the future.
 */
class ServerManager
{
    /**
     * @var string
     */
    protected $kernelRootDir;

    /**
     * @var string
     */
    protected $pidFile;

    /**
     * @var ProcessManager
     */
    protected $processManager;

    /**
     * @var \MockServer\Server\Primer
     */
    protected $primer;

    /**
     * @param Primer $primer         Primer*
     * @param string $kernelRootDir  Kernel Root Directory
     * @param string $kernelCacheDir Kernel Cache Directory
     * @param string $pidFile        Process ID Cache File
     */
    public function __construct(Primer $primer, $kernelRootDir, $kernelCacheDir, $pidFile)
    {
        $this->primer = $primer;

        $this->kernelRootDir = (string) $kernelRootDir;
        $this->kernelCacheDir = (string) $kernelCacheDir;
        $this->pidFile = (string) $kernelCacheDir . '/' . $pidFile;

        $this->processManager = new ProcessManager($this->pidFile);
    }

    /**
     * Create new server
     *
     * @param string $environment Symfony Environment
     * @param int    $port        Port
     * @param string $host        Host
     */
    public function create($environment, $port, $host = '127.0.0.1')
    {
        $cmd = __DIR__ . "/../bin/mockServer mock:server:start \"$this->kernelRootDir\" \"{$environment}\" \"{$host}\" {$port} \"{$this->pidFile}\"";
        exec($cmd . ' > /dev/null 2>&1 < /dev/null &', $output);
        sleep(1);

    }

    /**
     * Get pid file
     *
     * @return string
     */
    public function getPidFile()
    {
        return $this->pidFile;
    }

    /**
     * Set pid file
     *
     * @param string $pidFile
     *
     * @return ServerManager
     */
    public function setPidFile($pidFile)
    {
        $this->pidFile = (string) $pidFile;

        return $this;
    }

    /**
     * Get the process manager
     *
     * @return ProcessManager
     */
    public function getProcessManager()
    {
        return $this->processManager;
    }

    /**
     * Get Primer
     * @return \MockServer\Server\Primer
     */
    public function getPrimer()
    {
        return $this->primer;
    }
}
