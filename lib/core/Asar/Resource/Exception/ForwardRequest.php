<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_Resource_Exception_ForwardRequest extends Asar_Resource_Exception {
  
  private $payload = array('request' => null);
  
  function setPayload($payload) {
    $this->payload = array_merge($this->payload, $payload);
  }
  
  function getPayload() {
    return $this->payload;
  }
}
