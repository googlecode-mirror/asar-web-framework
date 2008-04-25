<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';
 
class Asar_RequestableTest extends PHPUnit_Framework_TestCase {

    function testSendTo() {
    	$this->req = new Asar_Request;
    	$respondent = $this->getMock('Asar_Requestable', array('handleRequest'));
    	$respondent->expects($this->once())
    	           ->method('handleRequest')
    	           ->with($this->req);
    	$this->req->sendTo($respondent);
    }

}