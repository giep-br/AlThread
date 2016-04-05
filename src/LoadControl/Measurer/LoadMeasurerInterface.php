<?php

namespace AlThread\LoadControl\Measurer;

use AlThread\LoadControl\Sensor\AbstractLoadSensor;

interface LoadMeasurerInterface
{
    public function setSensor(AbstractLoadSensor $sensor);
    public function measure();
}
