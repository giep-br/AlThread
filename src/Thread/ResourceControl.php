<?php
namespace AlThread\Thread;

use \AlThread\Thread\Exception\ResourceException;

/**
*   The ResourceControll is a resource supplier for the Threads
*/
class ResourceControl implements \Iterator, \Countable
{
    private $p;

    public function __construct($load)
    {
        $this->p = 0;
        $this->load = $load;
    }

    public function current()
    {
        return $this->load[$this->p];
    }

    public function key()
    {
        return $this->p;
    }

    public function next()
    {
        if (!$this->valid()) {
            throw new ResourceException("Resource pointer out of range");
        }

        $out = $this->current();
        $this->p++;
        return $out;
    }

    public function rewind()
    {
        $this->p = 0;
    }

    public function count()
    {
        return count($this->load);
    }

    public function valid()
    {
        return !($this->p >= $this->count());
    }
}
