<?php

require_once 'Asar.php';

class Test_Client extends Asar_Client {
  
}

class Asar_ClientTest extends PHPUnit_Framework_TestCase {
  private $temporary_storage = array();
	
	
  function setUp() {
    $this->DC = new Asar_Client();
    $this->TC = new Test_Client();
    
    // REFRESH PREDEFINED VALUES
    if (!array_key_exists('GET', $this->temporary_storage)) {
    	$this->temporary_storage['GET'] = array();
    	$this->arrayCopy($_GET, $this->temporary_storage['GET']);
    } else {
    	$this->arrayCopy( $this->temporary_storage['GET'], $_GET);
    }
    
    if (!array_key_exists('SERVER',  $this->temporary_storage)) {
    	$this->temporary_storage['SERVER'] = array();
      $this->arrayCopy($_SERVER, $this->temporary_storage['SERVER']);
    } else {
      $this->arrayCopy( $this->temporary_storage['SERVER'], $_SERVER);
    }
  }
  
  private function arrayCopy(&$from, &$to) {
  	// clear destination array first
  	$to = array();
  	foreach ($from as $key => $value) {
  		$to[$key] = $value;
  	}
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
    
    $request = $this->TC->createRequest($_SERVER['REQUEST_URI'],
      array(
        'params' => $expected_params,
        'method' => $_SERVER['REQUEST_METHOD'],
        'type'   => 'txt'
      )
    );
    $req_class = get_class($request);
    $this->assertEquals('Asar_Request', $req_class, 'Invalid object type. Must be \'Asar_Request\'. Returned '.$req_class);
    $this->assertEquals($expected_params, $request->getParams(), 'Unable to get params');
    $this->assertEquals('txt', $request->getType(), 'Unable to get type');
  }
  
  function testSetAndGetName() {
    $testname = 'A really cool name for a client';
    $this->DC->setName($testname);
    $this->assertEquals($testname, $this->DC->getName(), 'Client name did not match expected value');
  }
  
}
?>
