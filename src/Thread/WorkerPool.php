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
    private $terminated;
    private $ALT;

    public function __construct($max)
    {
        $this->max = $max;
        $this->pool = array();
        $this->terminated = 0;
        $this->ALT = array();
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

    public function getALT()
    {
        $tot = count($this->ALT);

        if (!$tot) {
            return 0;
        }

        $sum = array_reduce($this->ALT, function($carry, $item){
            $carry += $item;
            return $carry;
        });

        return $sum / $tot;
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

    public function getTerminated()
    {
        return $this->terminated;
    }

    public function getRunning()
    {
        $total = 0;
        foreach ($this->pool as $k => $t) {
            if ($t->isRunning()) {
                $total++;
            }
        }
        return $total;
    }

    public function collectGarbage()
    {
        foreach ($this->pool as $k => $t) {
            if (!$t->isRunning()) {
                $this->terminated++;
                $this->ALT[] = $t->getLT();
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
