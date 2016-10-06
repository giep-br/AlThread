<?php
namespace AlThread\Config;

use AlThread\Exception\ConfigException;

class ConfigControl implements \SplSubject
{
    private $config_vars;
    private $file;
    private $m_time;
    private $path;
    private $observers;

    public function __construct(\SplFileObject $file)
    {
        if (!$file->isFile()) {
            throw new ConfigException("Invalid config file");
        }
        $this->observers = [];
        $this->file = $file;
        $this->m_time = $this->file->getMtime();
        $this->inflateVars();
    }

    public function __get($attr)
    {
        if (!property_exists($this->config_vars, $attr)) {
                return null;
        }

        return $this->config_vars->$attr;
    }

    public function checkForFileChange()
    {
        $new_m_time = $this->file->getMtime();

        if ($new_m_time > $this->m_time) {
            $this->inflateVars();
            $this->m_time = $new_m_time;
            $this->notify();
        }
    }

    public function attach(\SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(\SplObserver $observer)
    {
        foreach ($this->observers as $k => $o) {
            if ($observer == $o) {
                unset($this->observers[$k]);
            }
        }
    }

    public function notify()
    {
        foreach ($this->observers as $k => $observer) {
            $observer->update($this);
        }
    }

    private function inflateVars()
    {
        $this->config_vars = ConfigLoader::loadConfig($this->file);
        $this->defaults();
    }

    private function validString($param)
    {
            return isset($param) and is_string($param);
    }

    private function validNumeric($param)
    {
            return isset($param) and is_numeric($param);
    }

    private function defaults()
    {
        if (!isset($this->config_vars->max_threads)) {
            $this->config_vars->max_threads = 10;
        }

        if (!isset($this->config_vars->min_threads)) {
            $this->config_vars->min_threads = 5;
        }

        if (!isset($this->config_vars->sensor_type)) {
            $this->config_vars->sensor_type = "load_avg";
        }

        if (!isset($this->config_vars->measurer_type)) {
            $this->config_vars->measurer_type = "first_degree";
        }
    }
}
