<?php
namespace AlThread\Thread;

use AlThread\Config\ConfigControl;
use AlThread\LoadControl\LoadControlMapper;

class Job
{
    private $config_file;
    private $config;
    private $thread_loop;
    private $is_setup = false;

    public function __construct($config_file, $workers_folder)
    {
        if (!is_dir($workers_folder)) {
            throw new Exception\ThreadException(
                "Invalid worker folder: ".$workers_folder
            );
        }

        $this->workers_folder = rtrim($workers_folder, "/");
        $this->config_file = $config_file;
        $file = new \SplFileObject($this->config_file);
        $this->config = new ConfigControl($file);
        $this->is_setup = false;
    }

    public function startJob()
    {
        if (!$this->is_setup) {
            throw new Exception\ThreadException(
                "Job not configured, call the method Job::setup()"
            );
        }

        $this->thread_loop->mainLoop();
    }

    protected function loadWorkerClass($class)
    {
        $file = $this->workers_folder.'/workers/'.$class.".php";

        if (!is_file($file)) {
            throw new Exception\ThreadException(
                "Worker file not exists"
            );
        }

        require_once($file);

        if (!class_exists($class)) {
            throw new Exception\ThreadException(
                "Worker class is not defined: ".$$class
            );
        }
    }

    public function setup()
    {
        try {
            $worker_class = $this->config->worker_class;
            $this->loadWorkerClass($worker_class);

            $sensor_type = $this->config->sensor_type;
            $measurer_type = $this->config->measurer_type;
            $max_threads = $this->config->max_threads;

            if (!$max_threads) {
                $max_threads = 10;
            }

            if (!$sensor_type) {
                $sensor_type = "load_avg";
            }

            if (!$measurer_type) {
                $measurer_type = "first_degree";
            }

            $sensor = LoadControlMapper::makeSensor($sensor_type);
            $measurer = LoadControlMapper::makeMeasurer($measurer_type, $max_threads);
            $measurer->setSensor($sensor);

            $pool = new WorkerPool($measurer->measure());

            $resources = $worker_class::setUpResource();
            $resource_controll = new ResourceControl($resources);

            $this->thread_loop = new ThreadLoop(
                $worker_class,
                $measurer,
                $pool,
                $resource_controll
            );
        } catch (\Exception $e) {
            echo $e;
        }
        $this->is_setup = true;
    }
}
