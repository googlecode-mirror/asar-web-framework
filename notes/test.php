<?php

class CustomException extends Exception {
  private $payload;
  
  function setPayload($payload) {
    $this->payload = $payload;
  }
  
  function getPayload() {
    return $this->payload;
  }

}

$payload = array(1,2,3);
$e = new CustomException('Message');
$e->setPayload($payload);
var_dump($e->getPayload());
throw $e;
