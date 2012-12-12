<?php

namespace MockServer\Process;

/**
 * Process Entity
 */
class Process
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * Constructor
     *
     * @param $id
     * @param $host
     * @param $port
     */
    public function __construct($id, $host, $port)
    {
        $this->id = (int) $id;
        $this->host = (string) $host;
        $this->port = (int) $port;
    }

    /**
     * Get id (pid)
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
}
