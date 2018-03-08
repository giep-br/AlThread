<?php

use AlThread\Mock\JobMock;
use AlThread\Thread\Context;

global $I; // I put $I as global because I'm using it on Worker Class.

require __DIR__ . '/fixtures/Worker.php';

$I = new FunctionalTester($scenario);
$I->wantTo('Create a job as a mock and run its worker.');

$uuid = uniqid();
$c = new Context();
$c->addItem('init', 1);
$c->addItem('end', 10);
$c->addItem("rand", $uuid );

$j = JobMock::make(
    '\Fixtures\Worker',
    $c,
    "load_avg",
    "first_degree",
    12,
    1,
    __DIR__ . "/fixtures",
    "job_mock"
);

$j->startJob();
