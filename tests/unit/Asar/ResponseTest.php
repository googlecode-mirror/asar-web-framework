<?php
require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';
 
class Asar_ResponseTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    $this->res = new Asar_Response();
  }
  
  function testSendTo() {    
    $respondent = $this->getMock('Asar_Respondable', array('processResponse'));
    $respondent->expects($this->once())
               ->method('processResponse')
               ->with($this->res);
    
    $this->res->sendTo($respondent);
  }
}


?>
