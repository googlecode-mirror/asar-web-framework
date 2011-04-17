<?php
namespace Asar\Resource\Exception;

use \Asar\Resource\Exception;

/**
 * @package Asar
 * @subpackage core
 */
class ForwardRequest extends Exception {
  
  private $payload = array('request' => null);
  
  function setPayload($payload) {
    $this->payload = array_merge($this->payload, $payload);
  }
  
  function getPayload() {
    return $this->payload;
  }
}
