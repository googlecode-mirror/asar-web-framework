<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Asar.php';

class Asar_Response extends Asar_Message {
  private $status_code = 200;
  
  function setStatusCode($code) {
  	// Check code against bounds 
  	if ($code >= 100 && $code <= 599) {
  	 $this->status_code = $code;
  	} else {
  		$this->exception('Attempting to set a status code that is unknown or out of bounds');
  	}
  }
  
  function getStatusCode() {
  	return $this->status_code;
  }
	
  function sendTo(Asar_Respondable $processor) {
    $this->setContext($processor);
    $processor->processResponse($this);
  }
  
  function setStatusOk() {
  	$this->setStatusCode(200);
  }
  
  function setStatusNotFound() {
    $this->setStatusCode(404);
  }
  
}

class Asar_Response_Exception extends Asar_Message_Exception {}
?>
