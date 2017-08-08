=======
# AlSmtp

[![CircleCi](https://circleci.com/gh/giep-br/AlThread.svg?style=shield&circle-token=b99734cd251d82737b31ef78dfa1237fa6534941)](https://circleci.com/gh/giep-br/AlThread)

A simple structure for creating Threaded jobs

## A Simple Example

### Implementing
- factorial.php
```php
use AlThread\Thread\Job;
use AlThread\Thread\Context;

$j = new Job(
    # Required folder structure ".../job/".
    "/path/to/jobs/conf/robo_teste.json",
    "/path/to/jobs/"
);

# Used to pass outer values to Threads
$c = new Context();
$c->addBulk("id", $argv[1], "time", $argv[2], "timeout", $argv[3]);

$j->setContext($c);
$j->setup();
$j->startJob();
```

### Worker
- jobs/workers/WorkerFact.php
```php
use AlThread\Thread\AbstractWorker;
use AlThread\Thread\Context;

class WorkerFact extends AbstractWorker
{

	public static function setUpResource(Context $context)
	{
	  return range(1, $context->getItem("max"));
	}

	protected function exec()
	{
		usleep(rand(0, 50000));
    echo "Factorial of {$this->line} = ";
		echo self::fact($this->line)."\n";
	}

	public static function onFinishLoop(Context $context){
     echo "\nEND.\n";
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
### Worker
- jobs/conf/job_fact.json
```json
{  
	"job_name" : "job_fact",
	"worker_class" : "WorkerFact",
	"measurer_type" : "first_degree",  
	"max_threads" : 10
}
```

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
    "giep-br/althread": ">=1.3.0"
  }
}
```
