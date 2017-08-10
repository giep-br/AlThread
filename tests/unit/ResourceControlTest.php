<?php
namespace ThreadTest;

use AlThread\Exception\ResourceException;
use \AlThread\Thread\ResourceControl;
use Codeception\TestCase\Test;

class ResourceControlTest extends Test
{
    /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
        $this->load = array(1, 2, 3, 4, 5);
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
        } catch (ResourceException $e) {
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

        $this->assertEquals(1, $resource->next());
        $this->assertTrue($resource->valid());

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

    public function testWrongLoad(){

      $message = 'ok';

      try {

        $resource = new ResourceControl(123);

      }catch( ResourceException $e ){

        $message = $e->getMessage();

      }

      $this->assertEquals($message, ResourceControl::LOAD_MUST_BE_ARRAY);

    }
}
