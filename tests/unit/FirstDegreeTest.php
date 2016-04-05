<?php
namespace MeasurerTest;

use AlThread;
use AlThread\Exception\MeasurerException;
use AlThread\LoadControl\Measurer\FirstDegree;
use Codeception\TestCase\Test;
use Codeception\Util\Stub;

class FirstDegreeTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->max_threads = 30;
        $this->firstDegree = new FirstDegree($this->max_threads);
    }

    protected function _after()
    {
    }

    public function testMeasure()
    {
        #Testing a raise without a LoadSensor defined
        try {
            $this->firstDegree->measure();
            $no_sensor = false;
        } catch (MeasurerException $e) {
            $no_sensor = true;
        }

        $this->assertTrue($no_sensor);

        $mockSensor = Stub::constructEmpty(
            '\AlThread\LoadControl\Sensor\LoadAVG',
            array("file" => "/proc/loadavg"),
            array('getSystemLoad' => Stub::consecutive(0, 1, 1.1, -1, null))
        );

        $this->firstDegree->setSensor($mockSensor);

        $this->assertEquals(30, $this->firstDegree->measure());
        $this->assertEquals(0, $this->firstDegree->measure());

        #a number greater than 1
        $this->assertEquals(0, $this->firstDegree->measure());

        #Testing a negative number raise Exception
        try {
            $this->firstDegree->measure();
        } catch (MeasurerException $e) {
            $wrong_negative_number = false;
            $wrong_negative_number = true;
        }

        $this->assertTrue($wrong_negative_number);

        #Testing a null var
        try {
            $this->firstDegree->measure();
            $wrong_null_number = false;
        } catch (MeasurerException $e) {
            $wrong_null_number = true;
        }
        $this->assertTrue($wrong_null_number);
    }

    public function testSetSensor()
    {
        #Testing if it throw a Exception when it's without a sensor
        $firstDegree = new FirstDegree($this->max_threads);

        try {
            $firstDegree->measure();
            $no_sensor = false;
        } catch (MeasurerException $e) {
            $no_sensor = true;
        }

        $this->assertTrue($no_sensor);
    }
}
