<?php
namespace AlThread\Thread;

use \AlThread\Exception\PoolException;

/**
*  The Pool is a context for running threads
*/
class WorkerPool
{
    private $pool;
    private $max;

    public function __construct($max)
    {
        $this->max = $max;
        $this->pool = array();
    }

    public function getSize()
    {
        return count($this->pool);
    }

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function submit(AbstractWorker $wk)
    {
        if (!$this->isFull()) {
            $this->pool[] = $wk;
            $wk->start();
        } else {
            throw new PoolException("The pool of threads is full");
        }
    }

    public function isFull()
    {
        return $this->getSize() >= $this->max;
    }

    public function collectGarbage()
    {
        foreach ($this->pool as $k => $t) {
            if (!$t->isRunning()) {
                unset($this->pool[$k]);
            }
        }
    }

    public function join()
    {
        while ($this->getSize()) {
            $this->collectGarbage();
        }
    }
}
