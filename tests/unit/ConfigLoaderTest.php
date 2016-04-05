<?php
namespace ConfigTest;

use AlThread\Config\ConfigLoader;
use AlThread\Exception\ConfigException;
use Codeception\TestCase\Test;
use Codeception\Util\Stub;

class ConfigLoaderTest extends Test
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
    }

    public function testLoadConfig()
    {
        /**
        *Assert that an exception was raised when a invalid json is used
        */
        $invalid_json = Stub::construct(
            "SplFileObject",
            array("filename" => "/tmp/loader_test.txt"),
            array(
                "current"=> Stub::consecutive("{\"attr\" 9}"),
                "valid" => Stub::consecutive(true, false)
            )
        );

        try {
            ConfigLoader::loadConfig($invalid_json);
            $wrong_json_except = false;
        } catch (ConfigException $e) {
            $wrong_json_except = true;
        }
        $this->assertTrue($wrong_json_except);

        $json_file = Stub::construct(
            "SplFileObject",
            array("filename" => "/tmp/loader_test.txt"),
            // Stubed Methods
            array(
                "current" => Stub::consecutive(
                    '{',
                    '"attr" : 8,',
                    '"object" : {"subattr" : 3}',
                    '}'
                ),
                "valid" => Stub::consecutive(
                    true, true, true, true,
                    true, true, true, false
                )
            )
        );

        /**
        *Verify succes object construct
        */
        $obj = ConfigLoader::loadConfig($json_file);
        $this->assertObjectHasAttribute("attr", $obj);
        $this->assertEquals(8, $obj->attr);
        $this->assertObjectHasAttribute("subattr", $obj->object);
        $this->assertEquals(3, $obj->object->subattr);
    }
}
