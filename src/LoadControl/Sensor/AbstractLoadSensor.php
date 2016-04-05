<?php

namespace AlThread\LoadControl\Sensor;

abstract class AbstractLoadSensor implements LoadSensorInterface
{
    public function getSystemLoad()
    {
        $load = $this->calculate();
        return $load;
    }

    abstract protected function calculate();
}
