<?php
namespace MockServer\Manager;

class ServerManager
{
    /**
     * @var string
     */
    protected $pidFile = '/tmp/MockServerPid';

    /**
     * @param null $pidFile
     */
    public function __construct($pidFile = null)
    {
        if (null !== $pidFile) {
            $this->pidFile = (string) $pidFile;
        }
    }

    public function create($server, $port, $host = '127.0.0.1', $devMode = false)
    {
        if (false === class_exists($server)) {
            throw new \InvalidArgumentException('Invalid server type: ' . $server);
        }

        if (true === $devMode) {
            $devMode = '--dev';
        }

        $cmd = __DIR__ . "/../bin/mockServer start \"{$server}\" \"{$host}\" {$port} \"{$this->pidFile}\" {$devMode}";
        exec($cmd . ' > /dev/null 2>&1 < /dev/null &', $output);
    }

    /**
     * @return string
     */
    public function getPidFile()
    {
        return $this->pidFile;
    }

    /**
     * @param string $pidFile
     */
    public function setPidFile($pidFile)
    {
        $this->pidFile = (string) $pidFile;

        return $this;
    }

    public function stopAll()
    {
        $processManager = new ProcessManager($this->pidFile);
        $processManager->flush(false);
    }
}
