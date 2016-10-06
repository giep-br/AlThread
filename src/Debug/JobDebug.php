<?php

namespace AlThread\Debug;


class JobDebug
{
    private
        $file,
        $start_at,
        $pool,
        $measurer,
        $job,
        $config,
        $id;

    public function __construct(
        \SplFileObject $file,
        \AlThread\Thread\WorkerPool $pool,
        \AlThread\Thread\Job $job,
        \AlThread\LoadControl\Measurer\LoadMeasurerInterface $measurer,
        \AlThread\LoadControl\Sensor\LoadSensorInterface $sensor,
        \AlThread\Config\ConfigControl $config,
        $id = ""
    ) {
        if(!$file->isWritable()) {
            throw new IOError("Debug File ".$file->getPathname()." is not a valid file.");
        }

        $this->file = $file;
        $this->start_at = date("Y-m-d H:i:s");
        $this->pool = $pool;
        $this->measurer = $measurer;
        $this->sensor = $sensor;
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
        return $this->measurer->measure();
    }

    public function getTerminated()
    {
        return $this->pool->getTerminated();
    }

    public function getALT()
    {
        return $this->pool->getALT();
    }

    public function getLAVG()
    {
        return $this->sensor->getSystemLoad();
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

    private function getData()
    {
        $out = "Job Id: ". $this->getID();
        $out .= " | Start At: ". $this->start_at;
        $out .= " \nTds Running: ". $this->getRunning();
        $out .= " | Sugested: " .$this->getSugested();
        $out .= " | Terminated: ". $this->getTerminated();
        $out .= " | ALT: ". $this->getALT()." s";
        $out .= " | LAVG: ". $this->getLAVG();
        $out .= " \nTds Max: ". $this->getMax();
        $out .= " | Tds Min: ". $this->getMin();
        $out .= "\n";
        return $out;
    }

    public function update()
    {
        $data = $this->getData();
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
