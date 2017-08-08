<?php
namespace AlThread\Thread;

interface WorkerInterface
{
    public function run();

    /**
     *
     * @param $context AlThread\Thread\Context
     * @return array [numerical]
     */
    public static function setUpResource(Context $context);

    /**
     *
     * @param $context AlThread\Thread\Context
     * @param $thread_return
     * @return array
     */
    public static function onFinishLoop(Context $context, $thread_return);
}
