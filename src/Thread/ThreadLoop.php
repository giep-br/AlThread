<?php
namespace AlThread\Thread;

use \AlThread\LoadControl\Measurer\AbstractLoadMeasurer;
use \AlThread\Debug\JobDebug;

class ThreadLoop
{
    private $worker_class;
    private $measurer;
    private $pool;
    private $resource;
    private $debuger;
    private $job_debug;

    public function __construct(
        $worker_class,
        AbstractLoadMeasurer $measurer,
        WorkerPool $pool,
        ResourceControl $resource,
        Context $context
    ) {
        $this->worker_class = $worker_class;
        $this->measurer = $measurer;
        $this->pool = $pool;
        $this->resource = $resource;
        $this->context = $context;
        $this->debuger = false;
    }

    private function output($text)
    {
        $date = new \DateTime();
        $out = "[";
        $out .= $date->format("d:m:Y H:i:s")."] - ";
        unset($date);

        $out .= $text;
        $out .= "\n";

        if ($this->debuger) {
            echo $out;
        }
    }

    public function setDebuger(JobDebug $debug)
    {
        $this->job_debug = $debug;
    }

    public function showDebuger($on)
    {
        $this->debuger = $on;
    }

    public function mainLoop()
    {
        while ($this->resource->valid()) {
            $this->pool->collectGarbage();
            while (!$this->pool->isFull() && $this->resource->valid()) {
                $this->pool->submit(new $this->worker_class(
                    $this->resource->key(),
                    $this->resource->next(),
                    $this->context
                ));
            }
            $this->pool->setMax($this->measurer->measure());
            if($this->debuger) {
                $this->job_debug->update();
            }
        }
        $this->pool->join();

        $worker_class = $this->worker_class;
        $worker_class::onFinishLoop($this->context, $this->pool->getThreadsOutput());
        $this->job_debug->close();
        return $this->pool->getThreadsOutput();
    }
}
