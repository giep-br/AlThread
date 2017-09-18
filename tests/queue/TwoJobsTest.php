<?php

use Codeception\TestCase\Test;
use Codeception\Util\Stub;

use AlThread\Thread\Job;
use AlThread\Thread\Context;

require_once __DIR__ . '/fixtures/WorkerFactA.php';
require_once __DIR__ . '/fixtures/WorkerFactB.php';

class FirstDegreeTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {

    }

    protected function _after()
    {
    }

    public function testCreate2Files()
    {

      $cA = new Context();
      $cA->addBulk("id_login_trans", 'a1');
      $cA->addBulk("id_login", 'a2');
      $cA->addBulk("filter", 'a3');
      $cA->addItem("max", 300);

      $jA = Job::make(
          //$worker_class| the logic of threads
          WorkerFactA::class,
          //$context| An object container
          $cA,
          //$sensor| The metric of machine load
          "load_avg",
          //$measurer| The threads measurer
          "first_degree",
          //$max_threads| Max Threads
          12,
          //$min_threads Min Threads
          1,
          //$debug_folder| A file that holds the Job status
          "/tmp/debug",
          //$job_id| Job ID
          "WorkerFactA_job"
      );

      $rA = $jA->startJob();

      $cB = new Context();
      $cB->addBulk("id_login_trans", 'a1');
      $cB->addBulk("id_login", 'a2');
      $cB->addBulk("filter", 'a3');
      $cB->addItem("max", 300);

      $jB = Job::make(
          //$worker_class| the logic of threads
          WorkerFactB::class,
          //$context| An object container
          $cB,
          //$sensor| The metric of machine load
          "load_avg",
          //$measurer| The threads measurer
          "first_degree",
          //$max_threads| Max Threads
          12,
          //$min_threads Min Threads
          1,
          //$debug_folder| A file that holds the Job status
          "/tmp/debug",
          //$job_id| Job ID
          "WorkerFactB_job"
      );

      $rB = $jB->startJob();
      exit(); // Just to give an echo on what was printed;
      $this->assertHasKey($rA,'A');
      $this->assertHasKey($rB,'B');


    }

}
