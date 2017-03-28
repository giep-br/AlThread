<?php

include("vendor/autoload.php");

use AlThread\Thread\Job;
use AlThread\Thread\Context;

$j = new Job(
    __DIR__ . "/jobs/conf/job_fact.json",
    __DIR__ . "/jobs/"
);

$c = new Context();
$c->addItem($argv[1], "max");
$j->setContext($c);
$j->setup();

$j->startJob();
$output = $j->getJobOutput();

print_r($output);
