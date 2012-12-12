<?php
namespace MockServer\Manager;

use Symfony\Component\Filesystem\Filesystem;
use MockServer\Process\ProcessIteratorAggregate;
use MockServer\Process\Process;

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
     * @var \MockServer\Process\ProcessIteratorAggregate
     */
    protected $processes;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

     /**
      * @param $processFile
      */
    public function __construct($processFile)
    {
        $this->processFile = (string) $processFile;
        $this->processes = new ProcessIteratorAggregate();
        $this->filesystem = new Filesystem();

        if (false === $this->filesystem->exists($this->processFile)) {
            file_put_contents($this->processFile, serialize($this->processes));
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
     * @return ProcessIteratorAggregate
     */
    public function load()
    {
        $this->processes = array();

        if (true === $this->filesystem->exists($this->processFile)) {
            $this->processes = unserialize(file_get_contents($this->processFile));
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
        $this->processes->add(
            new Process($pid, $host, $port)
        );

        return $this;
    }

    /**
     * Save to file
     *
     * @return ProcessManager
     */
    public function save()
    {
        file_put_contents($this->processFile, serialize($this->processes));

        return $this;
    }

    /**
     * Flush memory and the file of all processes - Kill when requested
     */
    public function flush($onlyDead = true, array $match = null)
    {
        $this->load();

        foreach ($this->processes as $process) {
            if (null === $match || $this->match($process, $match)) {
                if (true === $this->isActive($process)) {
                    if (true === $onlyDead) {
                        continue;
                    }
                    exec('kill -9 ' . $process->getId());
                }

                $this->processes->delete($process);
            }
        }

        $this->save();
    }

    /**
     * Check if a process is active by id
     *
     * @param Process $process
     * @return bool
     */
    public function isActive(Process $process)
    {
        exec('ps -p ' . $process->getId(), $isActive);

        if (count($isActive) > 1) {
            return true;
        }

        return false;
    }

    /**
     * Match Process
     *
     * @param Process $process
     * @param array $criteria
     * @return bool
     */
    protected function match(Process $process, array $criteria)
    {
        $matched = true;

        foreach ($criteria as $field => $value) {
            $method = 'get' . ucwords($field);
            if ($value !== $process->$method()) {
                $matched = false;
            }
        }

        return $matched;
    }
}
