<?php

include("./vendor/giep-br/althread/src/include.php");

use AlThread\Thread\Job;
use AlThread\Thread\Context;

$j = new Job(
    "./jobs/conf/job_fact.json",
    "./jobs/"
);

$c = new Context();
$c->addItem($argv[1], "max");
$j->setContext($c);
$j->setup();

$j->startJob();
