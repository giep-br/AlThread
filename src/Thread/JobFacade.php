<?php
namespace AlThread\Thread;

class JobFacade{

    private $loop;

    public function __construct()
    {
        $this->stop = false;
    }

    public function stop()
    {
        $this->stop = true;
    }

    public function isStoped()
    {
        return $this->stop;
    }

}
