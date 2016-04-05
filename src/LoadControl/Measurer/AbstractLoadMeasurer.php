<?php

namespace AlThread\LoadControl\Measurer;

use AlThread\Exception\MeasurerException;
use AlThread\LoadControl\Sensor;

abstract class AbstractLoadMeasurer implements LoadMeasurerInterface
{
    protected $root;
    private $sensor;
    const B = 1;

    public function __construct($root, $sensor = null)
    {
        $this->root = $root;
        $this->sensor = $sensor;
    }

    final public function setRoot($root)
    {
        $this->root = $root;
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

        if ($y > 1) {
            return 0;
        }

        return (int)$this->calculate($y);
    }

    abstract protected function calculate($y);
}
