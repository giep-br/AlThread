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
        $this->bootstrap();

        $this->exec($this->context);
    }

    /**
    *  Method to be rewrited in the child class
    */
    abstract protected function exec();

    private function bootstrap()
    {
        chdir(__DIR__);
        $previousDir = '.';

        while (!file_exists('vendor/autoload.php')) {
            $dir = dirname(getcwd());

            if ($previousDir === $dir) {
                throw new \RuntimeException(
                    'Unable to locate "vendor/autoload.php": ' .
                    'Please run composer install'
                );
            }

            $previousDir = $dir;
            chdir($dir);
        }

        /** @noinspection PhpIncludeInspection */
        require 'vendor/autoload.php';
    }
}
