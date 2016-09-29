<?php
namespace AlThread\Thread;

use AlThread\Config\ConfigControl;
use AlThread\Exception\ThreadException;
use AlThread\LoadControl\LoadControlMapper;
use AlTrhead\Debug\JobDebug;

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
    private $job_id;
    private $max_threads;
    private $min_threads;
    private $start_time;


    public function __construct($config_file, $workers_folder)
    {
        if (!is_dir($workers_folder)) {
            throw new ThreadException(
                "Invalid worker folder: ".$workers_folder
            );
        }

        $this->workers_folder = rtrim($workers_folder, "/");
        $this->config_file = $config_file;
        $file = new \SplFileObject($this->config_file);
        $this->config = new ConfigControl($file);
        $this->context = null;
        $this->is_setup = false;
        $this->start_time = time();
    }

    public function startJob()
    {
        if (!$this->is_setup) {
            throw new ThreadException(
                "Job not configured, call the method Job::setup()"
            );
        }

        $this->thread_loop->mainLoop();
    }

    protected function loadWorkerClass($fqcn)
    {
        $className = explode('\\', $fqcn);
        $file = $this->workers_folder . '/src/Workers/'
            . $className[count($className) - 1].".php";

        if (!is_file($file)) {
            throw new ThreadException(
                "Worker file not exists: ".$file
            );
        }

        require_once($file);

        if (!class_exists($fqcn)) {
            throw new ThreadException(
                "Worker class is not defined: \"".$fqcn."\""
            );
        }
    }

    public function update(\SplSubject $subject)
    {
        $this->config = $subject;
    }

    private function updateConfig()
    {
        $this->max_threads = $this->config->max_threads;
        if (!$this->max_threads) {
            $this->max_threads = 10;
        }

        $this->measurer->setRoot($this->max_threads);
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

    public function getJobId()
    {
        return $this->job_id;
    }

    public function setJobId($id)
    {
        $this->job_id = $id;
    }

    public function getStartTime()
    {
        return $this->start_time;
    }

    public function generateJobId()
    {
        if ($this->config->job_id) {
            return $this->config->job_id;
        }

        return md5( (string)time().(string)rand(0, 2000) );
    }

    public function setup()
    {
        $this->worker_class = $this->config->worker_class;
        $this->loadWorkerClass($this->worker_class);

        $sensor_type = $this->config->sensor_type;
        $measurer_type = $this->config->measurer_type;
        $this->max_threads = $this->config->max_threads;
        $this->min_threads = $this->config->min_threads;

        if (!$this->max_threads) {
            $this->max_threads = 10;
        }

        if (!$this->min_threads) {
            $this->min_threads = 0;
        }

        if (!$sensor_type) {
            $sensor_type = "load_avg";
        }

        if (!$measurer_type) {
            $measurer_type = "first_degree";
        }

        $this->job_id = (bool)$this->job_id ? $this->job_id : $this->generateJobId();

        $this->sensor = LoadControlMapper::makeSensor($sensor_type);
        $this->measurer = LoadControlMapper::makeMeasurer($measurer_type, $this->max_threads, $this->min_threads);
        $this->measurer->setSensor($this->sensor);

        $this->pool = new WorkerPool($this->measurer->measure());
        $worker_class = $this->worker_class;
        $resources = $worker_class::setUpResource($this->returnContext());
        $this->resource_controll = new ResourceControl($resources);
        $this->createThreadLoop();

        $this->is_setup = true;
    }

    private function createJobDebug()
    {
            $file = new \SplFileObject("/tmp/".$this->job_id, "w");
            $jd = new JobDebug(
                $file,
                $this->pool,
                $this,
                $this->measurer,
                $this->config
            );
            return $jd;
    }

    private function createThreadLoop()
    {
        $this->thread_loop = new ThreadLoop(
            $this->worker_class,
            $this->measurer,
            $this->pool,
            $this->resource_controll,
            $this->config,
            $this->returnContext(),
            $this->createJobDebug()
        );
    }
}
