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
        $this->min_threads = 10;
        $this->firstDegree = new FirstDegree($this->max_threads, $this->min_threads);
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
            array('getSystemLoad' => Stub::consecutive(0, 1, 2, -1, null, 0))
        );

        $this->firstDegree->setSensor($mockSensor);

        #Assert that a load 0 result in max_threads
        $this->assertEquals($this->max_threads, $this->firstDegree->measure());

        #Assert that a Load 1 result in min_threads
        $this->assertEquals($this->min_threads, $this->firstDegree->measure());

        #A that a load greater than 1 result in min threads
        $this->assertEquals($this->min_threads, $this->firstDegree->measure());

        #Assert that a negative load result in raise Exception
        try {
            $this->firstDegree->measure();
        } catch (MeasurerException $e) {
            $wrong_negative_number = true;
        }

        $this->assertTrue($wrong_negative_number);

        #Assert that null value load will result in exception
        try {
            $this->firstDegree->measure();
            $wrong_null_number = false;
        } catch (MeasurerException $e) {
            $wrong_null_number = true;
        }
        $this->assertTrue($wrong_null_number);
    }

    public function testSetMax()
    {
        #Testing a null var
        try {
            $this->firstDegree->setMax(null);
            $wrong_max = false;
        } catch (MeasurerException $e) {
            $wrong_max = true;
        }
        $this->assertTrue($wrong_max);
    }

    public function testSetMin()
    {
        #Testing a null var
        try {
            $this->firstDegree->setMin(null);
            $wrong_min = false;
        } catch (MeasurerException $e) {
            $wrong_min = true;
        }
        $this->assertTrue($wrong_min);
    }

    public function testSetSensor()
    {
        #Testing if it throw a Exception when it's without a sensor
        $firstDegree = new FirstDegree($this->max_threads, $this->min_threads);

        try {
            $firstDegree->measure();
            $no_sensor = false;
        } catch (MeasurerException $e) {
            $no_sensor = true;
        }

        $this->assertTrue($no_sensor);
    }
}
