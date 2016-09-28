<?php

namespace AlThread\LoadControl\Measurer;

use AlThread\Exception\MeasurerException;
use AlThread\LoadControl\Sensor;

abstract class AbstractLoadMeasurer implements LoadMeasurerInterface
{
    protected $root;
    protected $min;
    private $sensor;
    const B = 1;

    public function __construct($root, $min = null)
    {
        $this->root = $root;
        $this->min = $min;
    }

    final public function setRoot($root)
    {
        $this->root = $root;
    }

    final public function setMin($min)
    {
        $this->min = $min;
    }

    final public function setSensor(Sensor\AbstractLoadSensor $sensor)
    {
        $this->sensor = $sensor;
    }

    final public function measure()
    {
        if (!$this->sensor) {
            throw new MeasurerException("No sensor defined");
        }

        $y = $this->sensor->getSystemLoad();

        if ($y < 0 or !is_numeric($y)) {
            throw new MeasurerException("Invalid Sensor value");
        }

        $threads_to_run = (int)$this->calculate($y);

        if ($this->min and $threads_to_run < $this->min) {
            return $this->min;
        }

        return  $threads_to_run;
    }

    abstract protected function calculate($y);
}
