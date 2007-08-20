<?php

require_once 'Base.php';

class Asar_Request extends Asar_Base {
  private $request_method;
  private $address;
  private $contents;
  private $request_type;
  private $request_headers;
  
  const GET    = 1;
  const POST   = 2;
  const PUT    = 3;
  const DELETE = 4;
  
  
  
  function setAddress($application_name, $controller_name) {
    $this->address = array(
      'application' => $application_name,
      'controller'  => $controller_name);
  }
  
  
  function getAddress() {
    return $this->address;
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
    $this->request_method;
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
  
  
  function setType($type) {
    $this->request_type = $type;
  }
  
  
  function getType() {
    return $this->request_type;
  }
  
}

class Asar_Request_Exeption extends Asar_Base_Exception {}
?>
