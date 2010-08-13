<?php
require_once realpath(dirname(__FILE__) . '/../../../../config.php');

class Asar_Utility_StringTest extends PHPUnit_Framework_TestCase {

  private function _testFunction($method, $test_data) {
    $failmsg = "Asar_Utility_String::$method() did not return expected string.";
    foreach($test_data as $orig => $converted) {
      $this->assertEquals(
        $converted, 
        call_user_func(array('Asar_Utility_String', $method), $orig),
        $failmsg
      );
    }
  }  
  
  function testDashCamelCase() {
    $tests = array(
      'wanton-noodles' => 'Wanton-Noodles',
      'HOLy-maCaroni'  => 'Holy-Macaroni',
      'bAd'            => 'Bad',
      'foo'            => 'Foo',
      'BAR'            => 'Bar'
    );
    $this->_testFunction('dashCamelCase', $tests);
  }
  
  function testDashLowerCase() {
    $tests = array(
      'FooBar'         => 'foo-bar',
      'wanton-noodles' => 'wanton-noodles',
      'HOLy-maCaroni'  => 'h-o-ly-ma-caroni',
      'bAd'            => 'b-ad',
      'foo'            => 'foo',
      'BAR'            => 'b-a-r',
    );
    $this->_testFunction('dashLowerCase', $tests);
  }
  
  function testCamelCase() {
    $tests = array(
      'wanton-noodles' => 'WantonNoodles',
      'HOLy-maCaroni'  => 'HolyMacaroni',
      'camel case'     => 'CamelCase',
      'very_GoOd-food' => 'VeryGoodFood',
      'bAd'            => 'Bad',
      'foo'            => 'Foo',
      'BAR'            => 'Bar'
    );
    $this->_testFunction('camelCase', $tests);
  }
}
