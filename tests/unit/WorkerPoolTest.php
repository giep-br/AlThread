<?php
namespace ThreadTest;

use AlThread\Thread\WorkerPool;
use AlThread\Thread\AbstractWorker;
use AspectMock\Test as test;
use Codeception\Util\Stub;

require("../../src/Thread/WorkerInterface.php");
require("../../src/Thread/AbstractWorker.php");

class ConcretWorker extends AbstractWorker
{
    public static function setUpResource()
    {
        return null;
    }

    protected function exec()
    {
    }
}

class WorkerPoolTest extends \Codeception\TestCase\Test
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

    public function testConstruct()
    {
        $max_threads = 10;
        $pool = new WorkerPool($max_threads);

        $this->assertEquals($max_threads, $pool->getMax());
        $this->assertEquals(0, $pool->getSize());
    }

    public function testGetMax()
    {
        $max_threads = 2;
        $pool = new WorkerPool($max_threads);
        $this->assertEquals(2, $pool->getMax());
    }

    public function testGetSize()
    {
        $max_threads = 2;
        $pool = new WorkerPool($max_threads);

        $this->assertEquals(0, $pool->getSize());
        $pool->submit($this->stubConcreteWorker());
        $this->assertEquals(1, $pool->getSize());
        $pool->submit($this->stubConcreteWorker());
        $this->assertEquals(2, $pool->getSize());

        $max_threads_reach;
        try {
            $pool->submit($this->stubConcreteWorker());
            $max_threads_reach = false;
        } catch (\AlThread\Thread\Exception\PoolException $e) {
            $max_threads_reach = true;
        }
        $this->assertTrue($max_threads_reach);
    }

    public function testIsFull()
    {
        $max_threads = 2;
        $pool = new WorkerPool($max_threads);

        $pool->submit($this->stubConcreteWorker());
        $this->assertFalse($pool->isFull());
        $pool->submit($this->stubConcreteWorker());
        $this->assertTrue($pool->isFull());

    }

    public function testColectGarbage()
    {
        $max_threads = 2;
        $pool = new WorkerPool($max_threads);

        $pool->submit($this->stubIsRunningWorker());
        $pool->submit($this->stubIsRunningWorker());

        $this->assertEquals(2, $pool->getSize());
        $pool->collectGarbage();
        $this->assertEquals(0, $pool->getSize());
    }

    private function stubIsRunningWorker()
    {
        return  Stub::construct(
            "ThreadTest\ConcretWorker",
            array(),
            array(
                "start" => function () {},
                "isRunning" => Stub::consecutive(false),
            )
        );
    }

    private function stubConcreteWorker()
    {
        return  Stub::construct(
            "ThreadTest\ConcretWorker",
            array(),
            array(
                "start" => function () {}
            )
        );
    }

    private function stubWorkers()
    {
        $stubs = [];
        for ($i = 0; $i < 10; $i++) {
            $stubs[] = Stub::make(
                "AlThread\Thread\AbstractWorker",
                array(
                    "start" => function () {},
                    "exec" => function () {}
                )
            );
        }
    }
}
