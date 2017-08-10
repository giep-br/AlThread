<?php

use AlThread\Exception\PoolException;
use AlThread\Thread\Context;
use AlThread\Thread\WorkerPool;
use AlThread\Thread\AbstractWorker;

class ConcretWorker extends AbstractWorker
{
    public static function onFinishLoop(AlThread\Thread\Context $context, $thread_return){
        return null;
    }

    public static function setUpResource(Context $context)
    {
        return null;
    }

    protected function exec()
    {

    }
}
