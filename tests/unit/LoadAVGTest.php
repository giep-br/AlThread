<?php
namespace LoadMeasurerTest;

use AlThread;
use AlThread\Exception\SensorException;
use AlThread\LoadControl\Sensor\LoadAVG;

class LoadAVGTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->loadavg = new LoadAVG();
    }

    protected function _after()
    {
    }

    public function testConstruct()
    {
        #Test if a wrong file parameter raise an exception
        try {
            new LoadAVG("/shutup");
            $wrong_file = false;
        } catch (SensorException $e) {
            $wrong_file = true;
        }
        $this->assertTrue($wrong_file);
    }

    public function testGetSystemLoad()
    {
        $this->assertTrue($this->loadavg->getSystemLoad() >= 0);
    }
}
