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

            echo "  \n\n\n\n   ##### CHANGED CARAIIIII ##### \n\n\n";

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
    }
}
