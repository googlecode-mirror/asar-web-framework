<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

use Asar\Debug;

class Asar_DebugTest  extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->debug = new Debug;
  }
  
  function testBasicSet() {
    $this->debug->set('key', 'value');
    $this->assertEquals('value', $this->debug->get('key'));
  }
  
  function testDebugReturnsNullForUnknownKeys() {
    $this->assertSame(null, $this->debug->get('foo'));
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
