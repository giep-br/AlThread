<?php
namespace AlThread\Thread;

/**
*    AbstractWorker class will be inherited by the child class that will
* contains the logic of your Threads
*/
abstract class AbstractWorker extends \Thread implements WorkerInterface
{
    final public function run()
    {
        $this->exec();
    }

    /**
    *  Method to be rewrited in the child class
    */
    abstract protected function exec();

    abstract public static function setUpResource();
}
