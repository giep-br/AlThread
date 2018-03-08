<?php

namespace AlThread\Mock;

use AlThread\Thread\Context;

if(!class_exists('Thread')){
  require __DIR__ . '/Thread.php';
}

class Jobworker {

    protected $start_time;
    protected $context;
    protected $worker_class;

    public function __construct(Context $context, $worker_class){

        $this->context = $context;
        $this->worker_class = $worker_class;
        $this->start_time = microtime(true);
    }

    public function startJob(){

        $worker_class = $this->worker_class;

        $this->resources = $worker_class::setUpResource($this->context);

        $rOutput = [];

        foreach( $this->resources as $k => $resource ) {
            
            $w = new $worker_class($k, $resource, $this->context);
            $w->run();
            $rOutput[] = $w->getOutput();
            
        }

        return $worker_class::onFinishLoop($this->context, $rOutput);

    }

    public function getStartTime(){
        return $this->start_time;
    }
}

class JobMock {

    public static function make(
        $worker_class,
        Context $context,
        $sensor_type = "load_aveg",
        $measurer_type = "first_degree",
        $max_threads = 5,
        $min_threads = 1,
        $debug_folder = "/tmp/debug",
        $job_id = null
    ){

        return new JobWorker(
            $context,
            $worker_class
        );

    }
}