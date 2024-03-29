<?php
namespace Fact\Workers;

use AlThread\Thread\AbstractWorker;
use AlThread\Thread\Context;

class WorkerFact extends AbstractWorker
{
    public static function setUpResource(Context $context)
    {
        /*
        * The method setUpResource is responsible for
        * create the load that will be processed by
        * the threads, and it must return an array;
        */

        // The "max" attribute was defined in factorial.php
        $calculate_until = $context->getItem("max");

        // Create the array of numbers
        return range(1, $calculate_until);
    }

    protected function exec()
    {
        include(__DIR__."/../../vendor/autoload.php");
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
        /** This is the thread context, this method runs paralleling **/

        //Let's add some time of processing
        usleep(rand(0, 500000));
        echo "{$this->k }\n";
        if($this->k == 10){
            throw new \AlThread\Thread\JobException\StopJob();
        }
        /*
        * The atribute $this->line contains some item from the
        * array returned by SetUpResource to be processed,
        * in our case some number for calculate it's factorial
        */
        $factorial = self::fact($this->line);
        return array($this->line, $factorial);
    }

    public static function onFinishLoop(
        Context $context,
        $thread_return
    ){
        /** It will be called when the process done all the load **/

        // You can obtain the return of the processing in $thread_return
        print_r($thread_return);
    }

    private static function fact($x)
    {
        $num = range(1, $x);
        $out = 1;
        foreach($num as $v ){
            $out = $out * $v;
        }
        return $out;
    }
}
