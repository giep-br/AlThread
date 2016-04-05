<?php
namespace AlThread\Config;

use AlThread\Exception\ConfigException;

class ConfigLoader
{
    public static function loadConfig(\SplFileObject $file)
    {
        if (!$file->isFile()) {
            throw new ConfigException("Invalid configuration file");
        }

        $json = ConfigLoader::readFile($file);
        $obj = ConfigLoader::parseJson($json);

        if ($obj === null) {
            throw new ConfigException(
                "Parse error in json config file ".$file->getPathname()
            );
        }

        return $obj;
    }

    private static function readFile(\SplFileObject $file)
    {
        $out = "";
        foreach ($file as $line) {
            $out .= $line;
        }
        return $out;
    }

    private static function parseJson($json)
    {
        return json_decode($json);
    }
}
