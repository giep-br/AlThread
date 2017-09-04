<?php
namespace AlThread\Config;

class ConfigDefaults extends \Tunny\Config
{
    protected function defaults(){
        $config = array();
        $config["sensor_type"] = "load_avg";
        $config["measurer_type"] = "first_degree";
        $config["max_threads"] = "5";
        $config["min_threads"] = 1;
        $config["debug_folder"] = "/tmp/debug/";
        return $config;
    }
}
