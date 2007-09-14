<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Asar.php';

class Asar_Request extends Asar_Message {
  private $uri        = NULL;
  private $method     = NULL;
  
  const GET    = 'GET';
  const POST   = 'POST';
  const PUT    = 'PUT';
  const DELETE = 'DELETE';
  
  function setUri($uri) {
    $this->uri = $uri;
  }
  
  function getUri() {
    return $this->uri;
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
    if (is_array($contents)) {
      parent::setContent($contents);
    } else {
      $this->exception('Contents must be an associative array');
    }
  }
  
  function sendTo(Asar_Requestable $processor, array $arguments = NULL) {
    $this->setContext($processor);
    $processor->processRequest($this, $arguments);
  }
  
}

class Asar_Request_Exeption extends Asar_Message_Exception {}
?>
