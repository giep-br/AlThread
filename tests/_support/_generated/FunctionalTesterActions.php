<?php  //[STAMP] 835c0d2b2aeb325302ba33c2fb6e7129
namespace _generated;

// This class was automatically generated by build task
// You should not change it manually as it will be overwritten on next build
// @codingStandardsIgnoreFile

use Helper\Functional;

trait FunctionalTesterActions
{
    /**
     * @return \Codeception\Scenario
     */
    abstract protected function getScenario();

    
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Helper\Functional::seeExceptionThrown()
     */
    public function canSeeExceptionThrown($exception, $function) {
        return $this->getScenario()->runStep(new \Codeception\Step\ConditionalAssertion('seeExceptionThrown', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *
     * @see \Helper\Functional::seeExceptionThrown()
     */
    public function seeExceptionThrown($exception, $function) {
        return $this->getScenario()->runStep(new \Codeception\Step\Assertion('seeExceptionThrown', func_get_args()));
    }
}
