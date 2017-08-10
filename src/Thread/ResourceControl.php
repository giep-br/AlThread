<?php
namespace AlThread\Thread;

use \AlThread\Exception\ResourceException;

/**
*   The ResourceControll is a resource supplier for the Threads
*/
class ResourceControl implements \Iterator, \Countable
{
    private $p = 0;

    const LOAD_MUST_BE_ARRAY = 'The return of setUpResource must be a numerical array.';
    const OUT_OF_RANGE = 'Resource pointer out of range';

    public function __construct($load)
    {

      if( !(is_array($load) and isset($load)))
        throw new ResourceException(self::LOAD_MUST_BE_ARRAY);

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
        if (!$this->valid())
        {
            throw new ResourceException(self::OUT_OF_RANGE);
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
