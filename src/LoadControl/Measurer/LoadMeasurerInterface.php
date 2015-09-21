<?php
namespace AlThread\LoadControl\Measurer;

use AlThread\LoadControl\Sensor;

interface LoadMeasurerInterface
{
    public function setSensor(Sensor\AbstractLoadSensor $sensor);
    public function measure();
}
