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
  
  function testSettingAndGettingResponseStatus() {
  	// Test for 404, 200 HTTP response status equivalent
  	$this->res->setStatus(200);
  	$this->assertEquals(200, $this->res->getStatus(), 'Code not set properly');
    $this->res->setStatus(300);
    $this->assertEquals(300, $this->res->getStatus(), 'Code not set properly');
  }
  
  function testSettingStatusCodeThatIsGreaterThanTheRequiredBounds() {
  	try {
  		$this->res->setStatus(3000);
  		$this->assertTrue(false, 'Must not reach this point');
  	} catch (Asar_Base_Exception $e) {
  		$this->assertTrue($e instanceof Asar_Response_Exception, 'Wrong exception thrown');
  	}
  }
  
  function testSettingStatusCodeThatIsLessThanTheRequiredBounds() {
    try {
      $this->res->setStatus(10);
      $this->assertTrue(false, 'Must not reach this point');
    } catch (Asar_Base_Exception $e) {
      $this->assertTrue($e instanceof Asar_Response_Exception, 'Wrong exception thrown');
    }
  }
  
  function testSettingStatusSetsStatusCode() {
  	$this->res->setStatusOk();
  	$this->assertEquals(200, $this->res->getStatus(), 'Setting status to OK did not succeed');
  }
  
  function test200ShouldBeDefaultStatusCode() {
  	$this->assertEquals(200, $this->res->getStatus(), 'Status code 200 must be default');
  }
  
  function testSettingStatusNotFound() {
    $this->res->setStatusNotFound();
    $this->assertEquals(404, $this->res->getStatus(), 'Setting status to File Not Found did not succeed');
  }
	
	
}


?>
