<?php
namespace MockServer;

class Server
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host = '127.0.0.1', $port = 0)
    {
        $this->host = (string) $host;
        $this->port = (int) $port;
    }

    /**
     * Get mock server host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get mock server port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }
}
