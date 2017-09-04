<?php

namespace AlThread\LoadControl\Measurer;

use AlThread\Exception\MeasurerException;
use AlThread\LoadControl\Sensor;

abstract class AbstractLoadMeasurer implements LoadMeasurerInterface
{
    protected $max;
    protected $min;
    private $sensor;
    const B = 1;

    public function __construct($max, $min)
    {
        $this->setMax($max);
        $this->setMin($min);
    }

    final public function getMin() {
        return $this->min;
    }

    final public function getMax() {
        return $this->max;
    }

    final public function setMax($max)
    {
        if(!is_numeric($max)) {
            throw new MeasurerException("LoadMeassurer \$max must be an integer");
        }

        $this->max = (int)$max;
    }

    final public function setMin($min)
    {
        if(!is_numeric($min)) {
            throw new MeasurerException("LoadMeassurer \$min must be an integer");
        }

        $this->min = (int)$min;
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

        if ($threads_to_run < $this->min) {
            return $this->min;
        }

        return  $threads_to_run;
    }

    abstract protected function calculate($y);
}
