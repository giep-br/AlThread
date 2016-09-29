<?php
namespace AlThread\Debug;


class JobDebug
{
    public function __construct(
        \SplFileObject $file,
        \AlThread\Thread\WorkerPool $pool,
        \AlThread\Thread\Job $job,
        \AlThread\LoadControl\Meassurer\FirstDegree $meassurer,
        \AlThread\Config\ConfigControl $conf,
        $id = ""
    ) {
        if(!$file->isWritable()) {
            throw new IOError("Debug File ".$file->getPathname()." is not a valid file."    );
        }
        $this->file = $file;
        $this->pool = $pool;
        $this->meassurer = $meassurer;
        $this->job = $job;
        $this->config = $config;
        $this->id = $id;
    }

    public function getRunning()
    {
        return $this->pool->getRunning();
    }

    public function getSugested()
    {
        return $this->meassurer->meassure();
    }

    public function getTerminated()
    {
        return $this->pool->getTerminated();
    }

    public function getALT()
    {
        return $this->pool->getALT();
    }

    public function getMax()
    {
        return $this->config->max_threads;
    }

    public function getMin()
    {
        return $this->config->min_threads;
    }

    public function getID()
    {
        return $this->job->getJobId();
    }

    public function getStartTime()
    {
        return $this->job->getStartTime();
    }

    private function getData()
    {
        $out = "Job Id: ". $this->getID();
        $out .= " Tds Running: ". $this->getRunning();
        $out .= " Sugested: " .$this->getSugested();
        $out .= " Terminated: ". $this->getTerminated();
        $out .= " ALT: ". $this->getALT();
        $out .= " Tds Max: ". $this->getMax();
        $out .= " Tds Min: ". $this->getMin();
        $out .= "\n";
        return $out;
    }

    public function update()
    {
        $data = $thisw->getData();
        $this->file->rewind();
        $this->file->ftruncate(0);
        return $this->file->fwrite($data, 300);
    }

    public function close()
    {
        $file_path = $this->file->getPathname();
        $this->file = null;
        unlink($file_path);
    }
}
