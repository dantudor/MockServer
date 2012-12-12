<?php

namespace MockServer\Process;

/**
 * Process Iterator
 */
class ProcessIteratorAggregate implements \IteratorAggregate
{
    protected $processes = array();

    /**
     * Get Iterator
     *
     * @return ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->processes);
    }

    /**
     * Add
     *
     * @param Process $process
     * @return ProcessIteratorAggregate
     */
    public function add(Process $process)
    {
        $this->processes[$process->getId()] = $process;

        return $this;
    }

    /**
     * Delete
     *
     * @param Process $process
     * @return ProcessIteratorAggregate
     */
    public function Delete(Process $process)
    {
        unset($this->processes[$process->getId()]);

        return $this;
    }

    public function getById($id)
    {
        foreach ($this as $process) {
            if ($process->getId() === $id) {
                return $process;
            }
        }

        return null;
    }
}
