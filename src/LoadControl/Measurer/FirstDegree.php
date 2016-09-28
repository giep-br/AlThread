<?php

namespace AlThread\LoadControl\Measurer;

use AlThread\LoadControl\Sensor;

class FirstDegree extends AbstractLoadMeasurer
{

    private $a;

    public function __construct($root = 10, $min)
    {
        parent::__construct( $root, $min);
        $this->a = $this->foundAngular();
    }

    private function foundAngular()
    {
        return self::B/$this->root;
    }

    protected function calculate($y)
    {
        return( (-$y + 1) / $this->a);
    }
}
