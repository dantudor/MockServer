<?php

namespace MockServer\Manager;

use MockServer\Exception\InvalidServerException;

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

    /**
     * Create new server
     *
     * @param string $server
     * @param int $port
     * @param string $host
     * @param bool $devMode
     * @throws \InvalidArgumentException
     */
    public function create($server, $port, $host = '127.0.0.1', array $flags = null)
    {
        if (false === class_exists($server)) {
            throw new InvalidServerException('Invalid server type: ' . $server);
        }

        $flagString = '';
        if (null !== $flags) {
            foreach($flags as $flag) {
                $flagString .= ' ' . $flag;
            }
        }

        $cmd = __DIR__ . "/../bin/mockServer start \"{$server}\" \"{$host}\" {$port} \"{$this->pidFile}\" {$flagString}";
        exec($cmd . ' > /dev/null 2>&1 < /dev/null &', $output);
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
     */
    public function setPidFile($pidFile)
    {
        $this->pidFile = (string) $pidFile;

        return $this;
    }
}
