<?php
/**
 * @todo: Application, Controller, & Action names validation
 */
require_once 'Base.php';

class Asar_Request extends Asar_Base {
  private $request_method;
  private $address = NULL;
  private $action = NULL;
  private $contents = NULL;
  private $params = array();
  private $request_type;
  private $request_headers;
  private $uri = NULL;
  
  const GET    = 'GET';
  const POST   = 'POST';
  const PUT    = 'PUT';
  const DELETE = 'DELETE';
  /*
  static function createFromEnvironment($arguments) {
    $R = new self();
    return $R;
  }*/
  
  function setUri($uri) {
    $this->uri = $uri;
  }
  
  function getUri() {
    return $this->uri;
  }
  
  function setHeaders($headers) {
    if (is_array($headers)) {
      $this->request_headers = $headers;
    }
  }
  
  function getHeaders() {
    return $this->request_headers;
  }
  
  function setAddress($application_name, $controller_name = NULL) {
    $this->address = array(
      'application' => $application_name,
      'controller'  => $controller_name);
  }
  
  
  function getAddress() {
    return $this->address;
  }
  
  function setAction($action) {
    $this->action = $action;
  }
  
  function getAction() {
    return $this->action;
  }
  
  
  function setMethod($method) {
    switch ($method) {
      case self::GET:
      case self::POST:
      case self::PUT:
      case self::DELETE:
        $this->request_method = $method;
        return TRUE;
        break;
      default:
        $this->exception('Unknown method passed.');
    }
  }
  
  
  function getMethod() {
    return $this->request_method;
  }
  
  
  function setContent($contents) {
    if (is_array($contents)) {
      $this->contents = $contents;
    } else {
      $this->exception('Contents must be an associative array');
    }
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
    return $this->params[$key];
  }
  
  
  // @todo: Better mime-type setting ('text/plain', 'text/html', etc.)
  function setType($type) {
    $this->request_type = $type;
  }
  
  
  function getType() {
    return $this->request_type;
  }
  
}

class Asar_Request_Exeption extends Asar_Base_Exception {}
?>
