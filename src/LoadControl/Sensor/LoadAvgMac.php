<?php
namespace AlThread\LoadControl\Sensor;

use AlThread\LoadControl\Sensor\Exception;

class LoadAvgMac extends AbstractLoadSensor
{
    private function getTotalCores()
    {
        return (int)exec("sysctl -n hw.ncpu");
    }

    private function getOne()
    {
        $value = exec("sysctl -n vm.loadavg");
        $value = trim($value, ' \t\n\r\x0B');
        $content = explode(" ", $value);

        return $content[0];
    }

    protected function calculate()
    {
        return $this->getTotalCores() / $this->getOne();
    }
}
