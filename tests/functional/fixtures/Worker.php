<?php

namespace Fixtures;

use AlThread\Thread\AbstractWorker;
use AlThread\Thread\Context;

class Worker extends AbstractWorker {

  public static function setUpResource(Context $context) {

    $resource = [];

    $rangeInit = $context->getItem('init');
    $rangeEnd = $context->getItem('end');
    
    for( $i = $rangeInit; $i < $rangeEnd; $i++ ) {
        $resource[] = [
            'id' => $i,
            'name' => 'resource id: ' . $i,
            'job' => $context->getItem('rand'),
        ];
    }

    return $resource;
  }

  protected function exec() {

    global $I;

    $exected = [
        'line' => $this->line
    ];

    $line = $this->line['id'] - 1;

    $I->assertsame($this->k, $line, 'line id is one number more then resource line index.');

    return $exected;

  }

  public static function onFinishLoop(Context $context, $thread_return) {

    global $I;

    $numberOfLines = $context->getItem('end') - $context->getItem('init');

    $I->assertSame($numberOfLines, count($thread_return), 'number of thread returns should be the same as resources.');

  }

}