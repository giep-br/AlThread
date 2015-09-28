<?php
namespace ConfigTest;

use AlThread\Config\ConfigLoader;
use AlThread\Config\ConfigControl;
use AlThread\Config\Exception\ConfigException;
use AspectMock\Test as test;
use Codeception\Util\Stub;

class ConfigControlTest extends \Codeception\TestCase\Test
{
    /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
        exec("touch /tmp/loader_test.txt");

    }

    protected function _after()
    {
        exec("rm /tmp/loader_test.txt");
        test::clean();
    }

    public function testGet()
    {
        $file = $this->stubConfFile();
        $st_loader = $this->stubLoader();
        $config_control = new ConfigControl($file);

        $this->assertEquals(20, $config_control->threads);
        $this->assertEquals(80, $config_control->connections);
        $this->assertEquals(
            "value",
            $config_control->subObj->subAttr
        );
    }

    public function testUpdate()
    {
        $file = $this->stubConfFile();
        $st_loader = $this->stubLoader();
        $config_control = new ConfigControl($file);
        $this->assertEquals(20, $config_control->threads);

        $config_control->checkForFileChange();

        $this->assertEquals(20, $config_control->threads);

        $st_loader = $this->stubLoaderUpdate();
        $config_control->checkForFileChange();

        $this->assertEquals(40, $config_control->threads);
        $this->assertEquals(60, $config_control->connections);
        $this->assertEquals(2, $config_control->min_load);
    }

    protected function stubLoaderUpdate()
    {
        $json = '{
            "threads" : 40,
            "connections" : 60,
            "min_load" : 2
        }';

        return test::double(
            "AlThread\Config\ConfigLoader",
            ["loadConfig" => json_decode($json)]
        );
    }

    protected function stubLoader()
    {
        $json = '{
            "threads" : 20,
            "connections" : 80,
            "max_load" : 6,
            "subObj" : {
                "subAttr" : "value"
            }
        }';

        return test::double(
            "AlThread\Config\ConfigLoader",
            ["loadConfig" => json_decode($json)]
        );
    }

    protected function stubConfFile()
    {
        return Stub::construct(
            "\SplFileObject",
            array("filename" => "/tmp/loader_test.txt"),
            array("getMtime" => Stub::consecutive(
                1441324238,
                1441324238,
                1441324239
            ))
        );
    }
}
