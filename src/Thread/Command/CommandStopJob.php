<?php
namespace AlThread\Thread\Command;

class CommandStopJob extends AbstractCommand
{
    public function execute()
    {
        $this->system->stopJob();
    }
}
