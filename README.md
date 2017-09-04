# AlThread
![althread](https://github.com/giep-br/AlThread/blob/master/althread.png?raw=true "althread")

[![CircleCi](https://circleci.com/gh/giep-br/AlThread.svg?style=shield&circle-token=b3a2fbdc90581396fdba62d3077659b139cafb02)](https://circleci.com/gh/giep-br/AlThread)

### A simple structure for creating Threaded jobs
---


## Installation

Add to your composer.json:
```json

{
  "repositories": [
      {
          "type": "git",
          "url": "git@github.com:giep-br/AlThread.git"
      },
    ],
"require": {
    "giep-br/althread": "~2"
  }
}
```

## Sample

**factorial.php**
```php
<?php
// This example will calculate the factorial of the 300st numbers
include(__DIR__."/../vendor/autoload.php");

use AlThread\Thread\Job;
use AlThread\Thread\Context;
use Fact\Workers\WorkerFact;

//Passing an external value to the thread context
$context = new Context();
context->addItem("max", 300);

$j = Job::make(
    //$worker_class| the logic of threads
    WorkerFact::class,
    //$context| An object container
    $context,
    //$sensor| The metric of machine load
    "load_aveg",
    //$measurer| The threads measurer
    "first_degree",
    //$max_threads| Max Threads
    12,
    //$min_threads Min Threads
    1,
    //$debug_folder| A file that holds the Job status
    "/tmp/debug",
    //$job_id| Job ID
    "WorkerFact_job"
)

$output = $j->startJob();
print_r($output);
```
### Worker
**./workers/WorkerFact.php**
```php
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

        $calculate_until = $context->getItem("max");
        // Create the array of numbers
        return range(1, $calculate_until);
    }

    protected function exec()
    {
        /** This is the thread context, this method runs paralleling **/

        //Let's add some time of processing
        usleep(rand(0, 500000));

        /*
        * The atribute $this->line contains some item from the
        * array returned by SetUpResource to be processed,
        * in our case some number for calculate it's factorial
        */
        $factorial = self::fact($this->line)
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
```
**output**
```php
Array
(
    [0] => Array
        (
            [0] => 3
            [1] => 6
        )

    [1] => Array
        (
            [0] => 12
            [1] => 479001600
        )

    [2] => Array
        (
            [0] => 7
            [1] => 5040
        )
...
    [299] => Array
        (
            [0] => 287
            [1] => INF
        )
)
```

### Configure by file

*./conf/job_fact.json*
```json
{  
"job_name" : "job_fact",
"worker_class" : "WorkerFact",
"measurer_type" : "first_degree",  
"max_threads" : 10,
"debug_folder" : "/tmp/debug/",
"min_threads" : 5
}
```
*factorial.php*
```php
$j = Job::makeFromConf(
    // You can use .json .yml .ini and .php  as 0array
    __DIR__."/../conf/job_fact.json",
    WorkerFact::class,
    $context,
    "WorkerFact_job"
);
```
