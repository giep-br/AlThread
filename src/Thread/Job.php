<?php
namespace AlThread\Thread;

use AlThread\Config\ConfigControl;
use AlThread\LoadControl\LoadControlMapper;

class Job implements \SplObserver
{
    private $config_file;
    private $config;
    private $thread_loop;
    private $is_setup = false;
    private $worker_class;
    private $sensor;
    private $measurer;
    private $pool;
    private $resource_controll;
    private $context;

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
        $this->context = null;
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
                "Worker file not exists: ".$file
            );
        }

        require_once($file);

        if (!class_exists($class)) {
            throw new Exception\ThreadException(
                "Worker class is not defined: \"".$$class."\""
            );
        }
    }

    public function update(SplSubject $subject)
    {
        $this->config = $subject;
    }

    private function updateConfig()
    {
        $max_threads = $this->config->max_threads;
        if (!$max_threads) {
            $max_threads = 10;
        }

        $this->measurer->setRoot($max_threads);
    }

    private function returnContext()
    {
        if ($this->context === null) {
            return new Context();
        }

        return $this->context;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    public function setup()
    {
        try {
            $this->worker_class = $this->config->worker_class;
            $this->loadWorkerClass($this->worker_class);

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

            $this->sensor = LoadControlMapper::makeSensor($sensor_type);
            $this->measurer = LoadControlMapper::makeMeasurer($measurer_type, $max_threads);
            $this->measurer->setSensor($this->sensor);

            $this->pool = new WorkerPool($this->measurer->measure());
            $worker_class = $this->worker_class;
            $resources = $worker_class::setUpResource();
            $this->resource_controll = new ResourceControl($resources);
            $this->createThreadLoop();
        } catch (\Exception $e) {
            echo $e;
            die("\nexiting");
        }
        $this->is_setup = true;
    }

    private function createThreadLoop()
    {
        $this->thread_loop = new ThreadLoop(
            $this->worker_class,
            $this->measurer,
            $this->pool,
            $this->resource_controll,
            $this->config,
            $this->returnContext()
        );
    }
}
