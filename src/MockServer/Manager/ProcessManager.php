<?php
namespace MockServer\Manager;

use Symfony\Component\Filesystem\Filesystem;

class ProcessManager
{
    /**
     * @var string
     */
    protected $processFile;

    /**
     * @var array
     */
    protected $processes = array();

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
        $this->filesystem = new Filesystem();

        if (false == $this->filesystem->exists($this->processFile)) {
            $this->filesystem->touch($this->processFile);
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
     * Flush memory and the file of all processes - Kill when requested
     */
    public function flush($onlyDead = true, array $match = null)
    {
        $this->load();

        foreach ($this->processes as $process) {
            if (null === $match || $this->matchProcess($process, $match)) {
                if (true === $this->isActive($process->pid)) {
                    if (true === $onlyDead) {
                        continue;
                    }
                    exec('kill -9 ' . $process->pid);
                }

                unset($this->processes[$process->pid]);
            }
        }

        $this->save();
    }

    public function add($pid, $host, $port)
    {
        $process = array(
            'pid' => $pid,
            'host' => $host,
            'port' => $port
        );

        $this->processes[$pid] = $process;
    }

    /**
     * Save to file
     */
    public function save()
    {
        unlink($this->processFile);

        foreach ($this->processes as $process) {
            file_put_contents($this->processFile, json_encode($process) . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * load from file
     */
    public function load()
    {
        $this->processes = array();

        if (true === $this->filesystem->exists($this->processFile)) {
            $processes = json_decode(@file_get_contents($this->processFile));
            if (null !== $processes) {
                foreach ($processes as $process) {
                    $this->processes[$process->pid] = $process;
                }
            }
        }

        return $this->processes;
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
     * @param $process
     * @param array $criteria
     * @return bool
     */
    protected function matchProcess($process, array $criteria)
    {
        $matched = true;

        foreach ($criteria as $field => $value) {
            if ($value !== $process->$field) {
                $matched = false;
            }
        }

        return $matched;
    }
}
