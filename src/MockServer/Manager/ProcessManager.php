<?php
namespace MockServer\Manager;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Process Manager
 * This class manages the server instances at a process level
 */
class ProcessManager
{
    /**
     * @var string
     */
    protected $processFile;

    /**
     * @var \stdClass
     */
    protected $processes;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Monolog\Logger
     */
    protected $logger = null;

     /**
      * @param $processFile
      */
    public function __construct($processFile, Logger $logger = null)
    {
        $this->processes = new \stdClass();
        $this->processFile = (string) $processFile;
        $this->filesystem = new Filesystem();

        if (false === $this->filesystem->exists($this->processFile)) {
            file_put_contents($this->processFile, new \stdClass());
        }
    }

    /**
     * Get the Process File
     *
     * @return string
     */
    public function getProcessFile()
    {
        return $this->processFile;
    }

    /**
     * Load from process file
     *
     * @return array
     */
    public function load()
    {
        $this->processes = array();

        if (true === $this->filesystem->exists($this->processFile)) {
            $this->processes = json_decode(file_get_contents($this->processFile));
        }

        return $this->processes;
    }

    /**
     * Add a new process under management
     *
     * @param string $pid
     * @param string $host
     * @param int $port
     * @return ProcessManager
     */
    public function add($pid, $host, $port)
    {
        $process = new \stdClass();
        $process->pid = (string) $pid;
        $process->host = (string) $host;
        $process->port = (int) $port;

        $this->processes->{$pid} = $process;

        return $this;
    }

    /**
     * Save to file
     *
     * @return ProcessManager
     */
    public function save()
    {
        file_put_contents($this->processFile, json_encode($this->processes));

        return $this;
    }

    /**
     * Flush memory and the file of all processes - Kill when requested
     */
    public function flush($onlyDead = true, array $match = null)
    {
        $this->load();
        $processes = get_object_vars($this->processes);
        foreach ($processes as $process) {
            if (null === $match || $this->match($process, $match)) {
                if (true === $this->isActive($process->pid)) {
                    if (true === $onlyDead) {
                        continue;
                    }
                    exec('kill -9 ' . $process->pid);
                }

                unset($this->processes->{$process->pid});
            }
        }

        $this->save();
    }

    /**
     * Check if a process is active by id
     *
     * @param $pid
     * @return bool
     */
    public function isActive($pid)
    {
        exec('ps -p ' . $pid, $isActive);

        if (count($isActive) > 1) {
            return true;
        }

        return false;
    }

    /**
     * Match Process
     *
     * @param $process
     * @param array $criteria
     * @return bool
     */
    protected function match($process, array $criteria)
    {
        $matched = true;

        foreach ($criteria as $field => $value) {
            if ($value !== $process->$field) {
                $matched = false;
            }
        }

        return $matched;
    }

    /**
     * Sets the Monolog logger instance to be used for logging.
     *
     * @param \Monolog\Logger $logger
     * @codeCoverageIgnore
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}
