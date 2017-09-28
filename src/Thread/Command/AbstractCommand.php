<?php
namespace AlThread\Thread\Command;
use AlThread\Thread\SystemFacade;

abstract class AbstractCommand implements CommandInterface
{

    protected $system;

    public function __construct(SystemFacade $system)
    {
        $this->system = $system;
    }
}
