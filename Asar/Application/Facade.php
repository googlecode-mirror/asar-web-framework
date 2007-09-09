<?php
require_once 'Asar.php';

class Asar_Application_Facade extends Asar_Base {
  
  private $app = NULL;
  
  function createRequest($address, $arguments = NULL) {
    $req = new Asar_Request();
    $req->setUri($address);
    
    if (is_array($arguments)) {
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
  
  function getRequest($req = NULL) {
    if ($req) {
      // not normal request
    } else {
      // a normal request
      $address = $_SERVER['REQUEST_URI'];
      $arguments = array(
        'method'   => $this->getHttpRequestMethod(),
        'protocol' => $this->getProtocol(),
        'headers'  => $this->getHeaders()
      );
    }
  }
  
  protected function getHttpRequestMethod() {    
    if (isset($_SERVER['HTTP_METHOD'])) {
      $method = $_SERVER['HTTP_METHOD'];
    } else {
      $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : false;
    }
    return $method;
  }
  
  protected function getProtocol() {
    return isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : NULL;
  }
  
  
  protected function getHeaders($add_headers = NULL) {
    $headers = new array();
    foreach($_SERVER as $i=>$val) {
      if (strpos($i, 'HTTP_') === 0 || in_array($i, $add_headers)) {
        $name = str_replace(array('HTTP_', '_'), array('', '-'), $i);
        $headers[$name] = $val;
      }
    }
    return $headers;
  }
  
  function registerApplication($app_name) {
    $this->app = Asar::instantiate($app_name.'_Application');
  }
  
  function getRegisteredApplication() {
    return $this->app;
  }
}

?>
