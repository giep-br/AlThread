<?php
namespace AlThread\Thread;

/**
*    AbstractWorker class will be inherited by the child class that will
* contains the logic of your Threads
*/
abstract class AbstractWorker extends \Thread implements WorkerInterface
{
    protected $context;
    private $lifeTime;
    private $output;

    final public function __construct($k, $line, Context $context)
    {
        $this->context = $context;
        $this->k = $k;
        $this->line = $line;
        $this->lifeTime = null;
    }

    final public function run()
    {
        $start_time = microtime("now");
        $this->output = $this->exec($this->context);
        $this->lifeTime = microtime("now") - $start_time;
    }

    /**
    *  Method to be rewrited in the child class
    */
    abstract protected function exec();

    public function getLT()
    {
        if(!$this->lifeTime) {
            return null;
        }

        return $this->lifeTime;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
