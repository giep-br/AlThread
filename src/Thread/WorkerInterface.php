<?php
namespace AlThread\Thread;

interface WorkerInterface
{
    public function run();
    public static function setUpResource(Context $context);
}
