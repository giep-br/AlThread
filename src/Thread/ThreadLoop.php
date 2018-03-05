<?php
namespace AlThread\Thread;

use \AlThread\LoadControl\Measurer\AbstractLoadMeasurer;
use \AlThread\Debug\JobDebug;
use \AlThread\Thread\Command\CommandServer;

class ThreadLoop
{
    private $worker_class;
    private $measurer;
    private $pool;
    private $resource;
    private $command_server;
    private $debuger;
    private $job_debug;
    private $stop;

    public function __construct(
        $worker_class,
        AbstractLoadMeasurer $measurer,
        WorkerPool $pool,
        ResourceControl $resource,
        Context $context,
        CommandServer $command_server
    ) {
        $this->worker_class = $worker_class;
        $this->measurer = $measurer;
        $this->pool = $pool;
        $this->resource = $resource;
        $this->context = $context;
        $this->debuger = false;
        $this->command_server = $command_server;
        $this->stop = false;
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

    public function stop()
    {
        $this->stop = true;
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
        while ($this->resource->valid() && !$this->stop) {
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

            if($this->pool->hasExceptions())
            {
                foreach($this->pool->getThreadsExceptions() as $e) {
                    $cmd = $this->command_server->comandFromJobException($e);
                    $this->command_server->storeAndExecute($cmd);
                }
            }
        }

        $this->pool->join();
        $worker_class = $this->worker_class;

        $worker_class::onFinishLoop(
            $this->context,
            $this->pool->getThreadsOutput()
        );
        if($this->debuger) {
            $this->job_debug->close();
        }
        return $this->pool->getThreadsOutput();
    }
}
