<?php

namespace AlThread\LoadControl\Measurer;

use AlThread\LoadControl\Sensor;

class Maximizer extends AbstractLoadMeasurer
{
    protected function calculate($y)
    {
        return $this->max;
    }
}
