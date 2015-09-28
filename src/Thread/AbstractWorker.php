<?php
namespace AlThread\Thread;

/**
*    AbstractWorker class will be inherited by the child class that will
* contains the logic of your Threads
*/
abstract class AbstractWorker extends \Thread implements WorkerInterface
{
    private $context;

    final public function __construct($k, $line, Context $context)
    {
        $this->context = $context;
        $this->k = $k;
        $this->line = $line;
    }

    final public function run()
    {
        $this->exec($this->context);
    }

    /**
    *  Method to be rewrited in the child class
    */
    abstract protected function exec();
}
