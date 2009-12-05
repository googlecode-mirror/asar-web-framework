<?php
/**
 * Created on Jun 21, 2007
 * 
 * @author     Wayne Duran
 */

require_once 'Asar.php';

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
