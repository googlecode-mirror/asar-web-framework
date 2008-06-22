<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Asar.php';

class Asar_Response extends Asar_Message {
  private $status_code = 200;
  
  function setStatus($code) {
  	// Check code against bounds 
  	if ($code >= 100 && $code <= 599) {
  	 $this->status_code = $code;
  	} else {
  		$this->exception('Attempting to set a status code that is unknown or out of bounds');
  	}
  }
  
  function getStatus() {
  	return $this->status_code;
  }
  
  function setStatusOk() {
  	$this->setStatus(200);
  }
  
  function setStatusNotFound() {
    $this->setStatus(404);
  }
  
}
