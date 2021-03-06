<?php
namespace Asar\Resource\Exception;

use \Asar\Resource\Exception;

/**
 */
class Redirect extends Exception {
  
  private $payload = array('location' => null);
  
  function setPayload($payload) {
    $this->payload = array_merge($this->payload, $payload);
  }
  
  function getPayload() {
    return $this->payload;
  }
  
}
