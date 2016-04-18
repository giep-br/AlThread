<?php
namespace AlThread\Thread;

/**
*    AbstractWorker class will be inherited by the child class that will
* contains the logic of your Threads
*/
abstract class AbstractWorker extends \Thread implements WorkerInterface
{
    protected $context;

    final public function __construct($k, $line, Context $context)
    {
        $this->context = $context;
        $this->k = $k;
        $this->line = $line;
    }

    final public function run()
    {
        $this->bootstrap();

        $this->exec($this->context);
    }

    /**
    *  Method to be rewrited in the child class
    */
    abstract protected function exec();

    private function bootstrap()
    {
        /** @noinspection PhpIncludeInspection */
        require $this->findParentPath('vendor') . '/autoload.php';
    }

    private function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}
