<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar/Router.php';
 
class Asar_RouterTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->R = new Asar_Router();
  }
  
  function testBasicTranslateNativeRequest() {
    $test_req = 'basic/enactment/var1/val1/var2/val2.txt?enter=true$center=1&stupid&crazy=beautiful';    
    $expected_params = array(
      'var1' => 'val1',
      'var2' => 'val2');
    
    $result = $this->R->translate($test_req);
    
    $this->assertEquals('basic', $result['controller'], 'Unable to find controller');
    $this->assertEquals('enactment', $result['action'], 'Unable to find action');
    $this->assertEquals($expected_params, $result['params'], 'Unable to get params');
    $this->assertEquals('txt', $result['type'], 'Unable to get type');
  }
  
  
  function testCreateRequest() {
  }
}

?>