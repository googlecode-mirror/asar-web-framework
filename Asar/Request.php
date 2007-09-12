<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Asar.php';

class Asar_Request extends Asar_Message {
  private $controller = NULL;
  private $action     = NULL;
  private $uri        = NULL;
  
  function setUri($uri) {
    $this->uri = $uri;
  }
  
  function getUri() {
    return $this->uri;
  }
  
  function setHeaders($headers) {
    if (is_array($headers)) {
      $this->headers = $headers;
    }
  }
  
  function getHeaders() {
    return $this->headers;
  }
  
  function setAddress($application_name, $controller_name = NULL) {
    parent::setAddress($application_name);
    if (!is_null($controller_name)) {
      $this->setController($controller_name);
    }
  }
  
  function setController($controller_name) {
    $this->controller = $controller_name;
  }
  
  
  function getController() {
    return $this->controller;
  }
  
  
  function setAction($action) {
    $this->action = $action;
  }
  
  
  function getAction() {
    return $this->action;
  }
  
  
  function setContent($contents) {
    if (is_array($contents)) {
      parent::setContent($contents);
    } else {
      $this->exception('Contents must be an associative array');
    }
  }
  
  function sendTo(Asar_Requestable $processor) {
    $this->setContext($processor);
    $processor->processRequest($this);
  }
  
}

class Asar_Request_Exeption extends Asar_Message_Exception {}
?>
