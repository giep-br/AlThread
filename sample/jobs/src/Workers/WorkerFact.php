
<?php

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
        return array($this->line, self::fact($this->line));
	}

	public static function onFinishLoop(Context $context){
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
