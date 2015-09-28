<?php
namespace AlThread\Thread;

use \AlThread\LoadControl\Sensor\AbstractLoadSensor;
use \AlThread\LoadControl\Measurer\AbstractLoadMeasurer;
use \AlThread\Thread\Context;
use \AlThread\Config\ConfigControl;

class ThreadLoop
{
    private $worker_class;
    private $measurer;
    private $pool;
    private $resource;
    private $debuger;

    public function __construct(
        $worker_class,
        AbstractLoadMeasurer $measurer,
        WorkerPool $pool,
        ResourceControl $resource,
        ConfigControl $config,
        Context $context
    ) {
        $this->worker_class = $worker_class;
        $this->measurer = $measurer;
        $this->pool = $pool;
        $this->resource = $resource;
        $this->config = $config;
        $this->context = $context;
        $this->debuger = false;
    }

    private function output($text)
    {
        $date = new DateTime();
        $out = "[";
        $out .= $date->format("d:m:Y H:i:s")."] - ";
        unset($date);

        $out .= $text;
        $out .= "\n";

        if ($this->debuger) {
            echo $out;
        }
    }

    public function setDebuger($on)
    {
        $this->debuger = $on;
    }

    public function mainLoop()
    {
        while ($this->resource->valid()) {
            if (!$this->pool->isFull() && $this->resource->valid()) {
                $item = $this->resource->next();
                $k = $this->resource->key();
                $this->pool->submit(new $this->worker_class($k, $item, $this->context));
            }

            $this->config->checkForFileChange();

            $this->pool->setMax($this->measurer->measure());
            $this->pool->collectGarbage();
        }
        $this->pool->join();
    }
}
