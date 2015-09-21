<?php
namespace ThreadTest;

use \AlThread\Thread\ResourceControl;

class ResourceControlTest extends \Codeception\TestCase\Test
{
    /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
        $this->load = array(1, 2, 3, 4, 5);
    }

    protected function _after()
    {
    }

    public function testCurrent()
    {
        $resource = new ResourceControl($this->load);
        $this->assertEquals(1, $resource->current());

        $resource->next();

        $this->assertNotEquals(1, $resource->current());
        $this->assertEquals(2, $resource->current());
    }

    public function testKey()
    {
        $resource = new ResourceControl($this->load);
        $this->assertEquals(0, $resource->key());
        $resource->next();
        $this->assertEquals(1, $resource->key());
    }

    public function testNext()
    {
        $resource = new ResourceControl($this->load);
        $this->assertEquals(1, $resource->current());
        $this->assertEquals(0, $resource->key());

        $resource->next();

        $this->assertEquals(2, $resource->current());
        $this->assertEquals(1, $resource->key());

        try {
            for ($i = 0; $i < 6; $i++) {
                $resource->next();
            }
            $out_of_range = false;
        } catch (\AlThread\Thread\Exception\ResourceException $e) {
            $out_of_range = true;
        }
        $this->assertTrue($out_of_range);
    }

    public function testRewind()
    {
        $resource = new ResourceControl($this->load);
        $resource->next();
        $resource->next();
        $resource->next();

        $this->assertEquals(3, $resource->key());

        $resource->rewind();

        $this->assertEquals(0, $resource->key());
        $this->assertEquals(1, $resource->current());

    }

    public function testValid()
    {
        $resource = new ResourceControl($this->load);

        $this->assertEquals(2, $resource->next());
        $this->assertTrue($resource->valid());

        $this->assertEquals(3, $resource->next());
        $this->assertTrue($resource->valid());

        $this->assertEquals(4, $resource->next());
        $this->assertTrue($resource->valid());

        $this->assertEquals(5, $resource->next());
        $this->assertNotTrue($resource->valid());
    }

    public function testCount()
    {
        $resource = new ResourceControl($this->load);
        $this->assertEquals(5, count($resource));
    }
}
