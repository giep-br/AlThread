<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
  public function seeExceptionThrown($exception, $function)
  {
      try
      {
          $function();
          return false;
      } catch (Exception $e) {
        echo "\n\n\n\n";
        echo get_class($e);
        echo "\n\n\n\n";
        
          if( get_class($e) == $exception ){
              return true;
          }
          return false;
      }
  }
}
