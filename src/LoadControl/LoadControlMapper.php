<?php
namespace AlThread\LoadControl;

class LoadControlMapper
{
    public static $measurers = [
        "first_degree" => "\\AlThread\\LoadControl\\Measurer\\FirstDegree"
    ];

    public static $sensors = [
        "load_avg" => "\\AlThread\\LoadControl\\Sensor\\LoadAVG"
    ];

    public static function makeMeasurer($id, $max_threads)
    {
        if (!array_key_exists($id, self::$measurers)) {
            throw new \RuntimeException(
                "Invlalid Measurer class in config file"
            );
        }

        return new self::$measurers[$id]($max_threads);
    }

    public static function makeSensor($id)
    {
        if (!array_key_exists($id, self::$sensors)) {
            throw new \RuntimeException(
                "Invlalid Sensor class in config file"
            );
        }

        return new self::$sensors[$id]();
    }
}
