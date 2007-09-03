<?php

require_once 'Asar/Application/Facade.php';

class Asar_Application_FacadeTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->F = new Asar_Application_Facade();
  }
  
  function arrayMatch($arr1, $arr2) {
    if (count($arr1) !== count($arr2)) {
      return false;
    }
    foreach($arr1 as $key => $val) {
      if ($val !== $arr2[$key]) {
        return false;
      }
    }
    return true;
  }
  
  function testCreateRequest() {
    $address = 'people/get/asartalo/tags/reallyStupid/';
    $arguments = array(
      'method'  => 'GET',
      'headers' => array(
        'Accept'          => 'text/html',
        'Accept-Encoding' => 'gzip,deflate'
      ),
      'params'  => array(
        'var1'   => 'val1',
        'var2'   => 'val2',
        'enter'  => 'true',
        'center' => '1',
        'stupid' => '',
        'crazy'  => 'beautiful'
      )
    );
    $r = $this->F->createRequest($address, $arguments);
    $this->assertEquals($arguments['method'], $r->getMethod(), 'Method mismatch');
    $this->assertEquals($address, $r->getAddress(), 'Address mismatch');
    $this->assertTrue($this->arrayMatch($arguments['params'], $r->getParams()), 'Parameters did not match');
  }
  
  function testSendRequest() {
    $this->markTestIncomplete('To implement default Facade::sendRequest()');
  }
  
  function testDefaultGetResponse() {
    $this->markTestIncomplete('To implement default Facade::getResponse()');
    // Setup the environment
    $_SERVER['REQUEST_URI'] = 'basic/enactment/var1/val1/var2/val2.txt?enter=true$center=1&stupid&crazy=beautiful';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['enter']  = 'true';
    $_GET['center'] = '1';
    $_GET['stupid'] = '';
    $_GET['crazy']  = 'beautiful';
    
    $expected_params = array(
      'var1'   => 'val1',
      'var2'   => 'val2',
      'enter'  => 'true',
      'center' => '1',
      'stupid' => '',
      'crazy'  => 'beautiful'
    );
    
    $request = Asar_Request::createRequestFromEnvironment();
    
    $this->assertEquals('Asar_Request', get_class($request), 'Invalid object type. Must be \'Asar_Request\'');
    $this->assertEquals('basic', $request->address['controller'], 'Unable to find controller');
    $this->assertEquals('enactment', $request->address['action'], 'Unable to find action');
    $this->assertEquals($expected_params, $request->params, 'Unable to get params');
    $this->assertEquals('txt', $result['type'], 'Unable to get type');
  }
  
  function testGetResponseWithArguments() {
    $this->markTestIncomplete('To implement Facade::getResponse() with arguments');
  }
  
}
?>
