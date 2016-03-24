<?php
namespace AlThread\LoadControl\Sensor;

use AlThread\LoadControl\Sensor\Exception;

class LoadAVG extends AbstractLoadSensor
{
    private $load_file;
    private $file_handler;
    private $keys;
    private $one;
    private $core_numbers;

    public function __construct($file = "/proc/loadavg")
    {
        $this->load_file = $file;

        if (!is_file($this->load_file)) {
            throw new Exception\SensorException("Wrong file parameter");
        }

        $this->file_handler = fopen($this->load_file, 'r');
        $this->keys = ["one"];
        $this->core_numbers = $this->getTotalCores();
    }

    private function getTotalCores()
    {
        return (int)exec("lscpu|grep CPU\(s\)|cut -d' ' -f17|head -1");
    }

    private function readFile()
    {
        rewind($this->file_handler);
        return fread($this->file_handler, 30);
    }

    private function updateData()
    {
        $data = $this->parseFile();
        $this->one = $data['one'];
    }

    private function parseFile()
    {
        $content = explode(" ", $this->readFile());
        return array_combine($this->keys, [$content[0]]);
    }

    private function getOne()
    {
        $this->updateData();
        return $this->one;
    }

    protected function calculate()
    {
        return $this->getOne() / $this->core_numbers;
    }
}
