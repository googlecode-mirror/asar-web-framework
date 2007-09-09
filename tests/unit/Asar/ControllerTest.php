<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar/Controller.php';

class TestController extends Asar_Controller {
  function cool() {
    
  }
}

class Asar_ControllerTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
  }
  
  function testProcessRequest() {
    $this->markTestIncomplete('Not yet Implemented');
  }
  
  
}

?>