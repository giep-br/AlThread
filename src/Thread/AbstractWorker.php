<?php
namespace AlThread\Thread;

/**
*   AbstractWorker class will be inherited by the child class that will
* contains the logic of your Threads.
* @author Georgio Barbosa <georgio.barbosa@gmail.com>
*
*/
abstract class AbstractWorker extends \Thread implements WorkerInterface
{
    /**
    * @var AlThread\Thread\Context $context a value container.
    */
    protected $context;

    /**
    * @var int $lifeTime The thread that thread take to execute.
    */
    private $lifeTime;

    /**
    * @var mixed $output The thread return.
    */
    private $output;

    /**
    * @var AlThread\Thread\JobException\JobException $exception an JobException
    */
    private $exception;

    final public function __construct($k, $line, Context $context)
    {
        $this->context = $context;
        $this->k = $k;
        $this->line = $line;
        $this->lifeTime = null;
        $this->output = null;
        $this->exception = null;
    }

    final public function run()
    {
        $start_time = microtime("now");
        try {
            $this->output = $this->exec($this->context);
        } catch (\AlThread\Thread\JobException\JobException $exception) {
            $this->setException($exception);
        }
        $this->lifeTime = microtime("now") - $start_time;
    }

    /**
    *JobException\JobException $exception A JobException change the job behavior
    */
    final public function setException(JobException\JobException $exception)
    {
        $this->exception = $exception;
    }

    /**
    *  Method to be rewrited in the child class
    */
    abstract protected function exec();


    /**
    * @return The time thread take to execute
    */
    public function getLT()
    {
        if(!$this->lifeTime) {
            return null;
        }

        return $this->lifeTime;
    }

    /**
    * @return JobException\JobException;
    */
    public function getException()
    {
        return $this->exception;
    }

    /**
    * @return the thread output
    */
    public function getOutput()
    {
        return $this->output;
    }
}
