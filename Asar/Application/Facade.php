<?php
require_once 'Asar.php';

class Asar_Application_Facade extends Asar_Base {
  
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
}

?>
