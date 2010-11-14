<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_DebugTest  extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->debug = new Asar_Debug;
  }
  
  function testBasicSet() {
    $this->debug->set('key', 'value');
    $this->assertEquals('value', $this->debug->get('key'));
  }
  
  function testIteration() {
    $this->debug->set('foo', 1);
    $this->debug->set('bar', 'B');
    $str = '';
    foreach ($this->debug as $key => $value) {
      $str .= "$key: $value; ";
    }
    $this->assertEquals('foo: 1; bar: B; ', $str);
  }
  
}