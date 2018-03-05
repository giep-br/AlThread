<?php

namespace AlThread\Mock;

use AlThread\Thread\Context;

if(!class_exists('Thread')){
  require __DIR__ . '/Thread.php';
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

        $resources = $worker_class::setUpResource($context);

        $rOutput = [];

        foreach( $resources as $k => $resource ) {

            $w = new $worker_class($k, $resource, $context);
            $w->run();
            $rOutput[] = $w->getOutput();
            
        }

        $worker_class::onFinishLoop($context, $rOutput);

    }
}