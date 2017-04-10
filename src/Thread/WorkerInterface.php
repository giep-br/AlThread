<?php
namespace AlThread\Thread;

interface WorkerInterface
{
    public function run();
    public static function setUpResource(Context $context);
    public static function onFinishLoop(Context $context, $thread_return);
}
