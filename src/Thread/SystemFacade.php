<?php
namespace AlThread\Thread;

class SystemFacade
{
    private $job;
    private $pool;
    private $loop;
    private $resource;

    public function setThreadLoop(ThreadLoop $loop)
    {
        $this->loop = $loop;
    }

    public function setJob(Job $job)
    {
        $this->job = $job;
    }

    public function setWorkerPool(WorkerPool $pool)
    {
        $this->pool = $pool;
    }

    public function setResourceControl(ResourceControl $resource)
    {
        $this->resource = $resource;
    }

    public function stopJob()
    {
        $this->loop->stop();
    }
}
