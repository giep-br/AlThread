<?php
namespace AlThread\Thread;

use AlThread\Exception\ContextException;

class Context
{
    private $vars;

    public function addBulk($key, $item)
    {
        $cont_key = null;
        foreach (func_get_args() as $k => $v) {
            if ($k % 2 == 0) {
                $cont_key = $v;
            } else {
                $this->addItem($cont_key, $v);
                $cont_key = null;
            }
        }
    }

    public function delItem($key)
    {
        if (!$this->checkItemexists($key)) {
            throw new ContextException("Key $key do not exists.");
        }

        unset($this->vars[$key]);
    }

    public function addItem($key = null, $obj)
    {
        if ($key == null) {
            $this->vars[] = $obj;
        } else {
            if ($this->checkItemexists($key)) {
                throw new ContextException("Key $key already in use.");
            } else {
                $this->vars[$key] = $obj;
            }
        }
    }

    public function checkItemExists($key)
    {
        return isset($this->vars[$key]);
    }

    public function getItem($key)
    {
        if (!$this->checkItemexists($key)) {
            throw new ContextException("Key $key do not exists.");
        }

        return $this->vars[$key];
    }
}
