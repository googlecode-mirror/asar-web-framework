<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Asar.php';

abstract class Asar_Message extends Asar_Base {
  private $method     = NULL;
  private $address    = NULL;
  private $contents   = NULL;
  private $params     = array();
  private $type       = NULL;
  private $headers    = array();
  private $context    = array();
  
  const GET    = 'GET';
  const POST   = 'POST';
  const PUT    = 'PUT';
  const DELETE = 'DELETE';
  
  function setHeaders($headers) {
    if (is_array($headers)) {
      $this->headers = $headers;
    }
  }
  
  function getHeaders() {
    if (!(count($this->headers) > 0)) {
      $this->exception('Headers not set');
    } else {
      return $this->headers;
    }
  }
  
  function setAddress($address) {
    $this->address = $address;
  }  
  
  function getAddress() {
    if (is_null($this->address)) {
      $this->exception('Address not set');
    } else {
      return $this->address;
    }
  }
  
  function setMethod($method) {
    switch ($method) {
      case self::GET:
      case self::POST:
      case self::PUT:
      case self::DELETE:
        $this->method = $method;
        return TRUE;
        break;
      default:
        $this->exception('Unknown method passed.');
    }
  }
  
  
  function getMethod() {
    if (is_null($this->method)) {
      $this->exception('Method is not set');
    } else {
      return $this->method;
    }
  }
  
  
  function setContent($contents) {
      $this->contents = $contents;
  }
  
  function getContent() {
    return $this->contents;
  }
  
  // For query strings
  function setParams($params) {
    if (is_array($params)) {
      $this->params = array_merge($this->params, $params);
    } else {
      $this->exception('Params must be an associative array');
    }
  }
  
  function getParams() {
    return $this->params;
  }
  
  function setParam($key, $value) {
    $this->params[$key] = $value;
  }
  
  function getParam($key) {
    if (array_key_exists($key, $this->params)) {
      return $this->params[$key];
    } else {
      $this->exception("The parameter '$key' specified does not exist");
    }
  }
  
  
  // @todo: Better mime-type setting ('text/plain', 'text/html', etc.)
  function setType($type) {
    $this->type = $type;
  }
  
  
  function getType() {
    if (is_null($this->type)) {
      $this->exception('Type not set');
    } else {
      return $this->type;
    }
  }
  
  protected function setContext($processor) {
    $this->context = $processor;
  }
  
  function getCurrentContext() {
    return $this->context;
  }
  
}

class Asar_Message_Exception extends Asar_Base_Exception {}
?>
