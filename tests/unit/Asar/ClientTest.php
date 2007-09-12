<?php

require_once 'Asar.php';

class Test_Client extends Asar_Client {
  
}

class Asar_ClientTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->DC = new Asar_Client();
    $this->TC = new Test_Client();
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
    $r = $this->DC->createRequest($address, $arguments);
    $this->assertEquals($arguments['method'], $r->getMethod(), 'Method mismatch');
    $this->assertEquals($address, $r->getUri(), 'Address mismatch');
    $this->assertTrue($this->arrayMatch($arguments['params'], $r->getParams()), 'Parameters did not match');
  }
  
  function testSendRequest() {
    $this->markTestIncomplete('Send Request not implemented');
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
    
    $request = $this->TC->createRequest();
    $req_class = get_class($request);
    $this->assertEquals('Asar_Request', $req_class, 'Invalid object type. Must be \'Asar_Request\'. Returned '.$req_class);
    $this->assertEquals('basic', $request->address['controller'], 'Unable to find controller');
    $this->assertEquals('enactment', $request->address['action'], 'Unable to find action');
    $this->assertEquals($expected_params, $request->params, 'Unable to get params');
    $this->assertEquals('txt', $result['type'], 'Unable to get type');
  }
  
  function testSetAndGetName() {
    $testname = 'A really cool name for a client';
    $this->DC->setName($testname);
    $this->assertEquals($testname, $this->DC->getName(), 'Client name did not match expected value');
  }
  
}
?>
