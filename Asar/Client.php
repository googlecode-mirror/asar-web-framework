<?php
require_once 'Asar.php';

class Asar_Client extends Asar_Base {
  
  private $app  = NULL;
  private $name = NULL;
  
  function createRequest($address, $arguments = NULL) {
    $req = new Asar_Request();    
    $req->setUri($address);
    
    if (is_array($arguments)) {
      if (array_key_exists('controller', $arguments)) {
        $req->setController($arguments['controller']);
      }
      
      if (array_key_exists('method', $arguments)) {
        $req->setMethod($arguments['method']);
      }
      
      if (array_key_exists('headers', $arguments)) {
        $req->setHeaders($arguments['headers']);
      }
      
      if (array_key_exists('content', $arguments)) {
        $req->setContent($arguments['content']);
      }
      
      if (array_key_exists('params', $arguments)) {
        $req->setParams($arguments['params']);
      }
    }
    
    return $req;
  }
  
  function setName($name) {
    if (!is_string($name)) {
      $this->exception('Name passed to setName must be a string');
    } else {
      $this->name = $name;
    }
  }
  
  function getName() {
    if (is_null($this->name)) {
      $this->exception('No name for client was set');
    } else {
      return $this->name;
    }
  }
  
  function sendRequest() {
    
  }
  
  function setServiceProvider(Asar_Application $app) {
    $this->app = $app;
  }
  
}

class Asar_Client_Exception extends Asar_Base_Exception {}

?>
