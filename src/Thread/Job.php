<?php
namespace AlThread\Thread;

use AlThread\Exception\ThreadException;
use AlThread\LoadControl\LoadControlMapper;
use AlThread\Debug\JobDebug;
use \AlThread\LoadControl\Measurer\AbstractLoadMeasurer;

class Job
{
    private $is_setup = false;
    private $thread_loop;
    private $job_id;
    private $start_time;

    public function __construct(
        ThreadLoop $loop,
        $job_id = null
    )
    {
        $this->job_id = $job_id;
        $this->is_setup = false;
        $this->start_time = microtime(true);
        $this->thread_loop = $loop;
    }

    public function startJob()
    {
        if (!$this->is_setup) {
            throw new ThreadException(
                "Job not configured, call the method Job::setup()"
            );
        }
        return $this->thread_loop->mainLoop();
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

    public function setup()
    {
        $this->job_id = (bool)$this->job_id ? $this->job_id : $this->generateJobId();
        $this->is_setup = true;
    }

    private function generateJobId()
    {
        if ($this->job_id) {
            return $this->job_id;
        }
        return md5( (string)time().(string)rand(0, 2000) );
    }

    private static function createJobDebug(
        $job_id,
        WorkerPool $pool,
        AbstractLoadMeasurer $measurer,
        $debug_folder
    ){
        if(!is_dir($debug_folder)) {
            throw new \AlThread\Exception\ConfigException("Invalid config value debug_folder: {$debug_folder}");
        }

        $debug_folder = rtrim($debug_folder, "/ ");

        $file = new \SplFileObject("$debug_folder/$job_id.dbg", "w");
        $jd = new JobDebug(
            $file,
            $pool,
            $measurer,
            $job_id
        );
        return $jd;
    }

    public static function make(
        $worker_class,
        Context $context,
        $sensor_type = "load_aveg",
        $measurer_type = "first_degree",
        $max_threads = 5,
        $min_threads = 1,
        $debug_folder = "/tmp/debug",
        $job_id = null
    )
    {
        $sensor = \AlThread\LoadControl\LoadControlMapper::makeSensor($sensor_type);

        $measurer = \AlThread\LoadControl\LoadControlMapper::makeMeasurer(
            $measurer_type,
            $max_threads,
            $min_threads
        );
        $measurer->setSensor($sensor);

        $pool = new WorkerPool($measurer->measure());

        $resources = $worker_class::setUpResource($context);
        $resource_controll = new ResourceControl($resources);

        $thread_loop = new ThreadLoop(
            $worker_class,
            $measurer,
            $pool,
            $resource_controll,
            $context
        );

        $job_debug = self::createJobDebug(
            $job_id,
            $pool,
            $measurer,
            $debug_folder
        );

        $thread_loop->setDebuger($job_debug);
        $thread_loop->showDebuger(true);

        $job = new static(
            $thread_loop
        );
        $job_debug->setJob($job);
        $job->setJobId($job_id);
        $job->setup();

        return $job;
    }

    public static function makeFromConf(
        $config_path,
        $worker_class,
        Context $context,
        $job_id = null
    )
    {
        $config = \AlThread\Config\ConfigDefaults::make($config_path);
        return self::make(
            $worker_class,
            $context,
            $config['sensor_type'],
            $config['measurer_type'],
            $config['max_threads'],
            $config['min_threads'],
            $config['debug_folder'],
            $job_id
        );
    }
}
