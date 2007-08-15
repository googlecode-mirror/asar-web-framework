<?php

require_once 'Base.php';

class Asar_Request extends Asar_Base {
  private $request_method;
  private $address;
  private $contents;
  private $request_type;
  
  
  
  
  function setAddress($application_name, $controller_name) {
    $this->address = array(
      'application' => $application_name,
      'controller'  => $controller_name);
  }
  
  
  function getAddress() {
    return $this->address;
  }
  
  
  function setRequestMethod($method = 'GET') {
    // @todo maybe implement this as class constants for method values
    switch ($method) {
      case 'GET':
      case 'POST':
      case 'PUT':
      case 'DELETE':
        $this->request_method = $method;
        return TRUE;
        break;
      default:
        // @todo Raise exception
        return FALSE;
    }
  }
  
  
  function getRequestMethod() {
    $this->request_method;
  }
  
  
  function setContent($contents) {
    if (is_array($contents)) {
      $this->contents = $contents;
    } else {
      // @todo Raise Exception
    }
  }
  
  function getContent() {
    return $this->contents;
  }
  
}

?>