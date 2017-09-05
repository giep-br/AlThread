<?php
// This example will calculate the factorial of the 300st numbers
include(__DIR__."/../vendor/autoload.php");

use AlThread\Thread\Job;
use AlThread\Thread\Context;
use Fact\Workers\WorkerFact;

//Passing an external value to the thread context
$context = new Context();
$context->addItem("max", 300);

$j = Job::make(
    //$worker_class| the logic of threads
    WorkerFact::class,
    //$context| An object container
    $context,
    //$sensor| The metric of machine load
    "load_avg",
    //$measurer| The threads measurer
    "first_degree",
    //$max_threads| Max Threads
    12,
    //$min_threads Min Threads
    1,
    //$debug_folder| A file that holds the Job status
    "/tmp/debug",
    //$job_id| Job ID
    "WorkerFact_job"
);

$output = $j->startJob();
$t = microtime(true) - $j->getStartTime();
echo "# ". count($output)." factorials calculated, in $t seconds.";
