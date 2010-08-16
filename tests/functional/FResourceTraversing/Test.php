<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FResourceTraversing_Test extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->client = new Asar_Client;
    $f = new Asar_ApplicationFactory(new Asar_Config_Default);
    $this->app = $f->getApplication('ResourceTraversing');
  }
  
  function testBasic() {
    $this->markTestIncomplete();
  }

}
