<?php

include(__DIR__."/../vendor/autoload.php");

use AlThread\Thread\Job;
use AlThread\Thread\Context;
use Fact\Workers\WorkerFact;

$c = new Context();
$c->addItem("max", 200);

$j = Job::makeFromConf(
    __DIR__."/../conf/job_fact.json",
    WorkerFact::class,
    $c,
    "WorkerFact"
);

$output = $j->startJob();

$t = microtime(true) - $j->getStartTime();
echo "# ". count($output)." factorials calculated, in $t seconds.";
