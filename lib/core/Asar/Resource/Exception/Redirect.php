<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_Resource_Exception_Redirect extends Asar_Resource_Exception {
  
  private $payload = array('location' => null);
  
  function setPayload($payload) {
    $this->payload = array_merge($this->payload, $payload);
  }
  
  function getPayload() {
    return $this->payload;
  }
  
}
