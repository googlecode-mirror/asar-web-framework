<?php

class Asar_Client {

  function sendRequest($server, $request) {
    if ($server instanceof Asar_Resource_Interface) {
      return $server->handleRequest($request);
    }
    throw new Asar_Client_Exception_UnknownServerType;
  }
  
  private function sendRequestByMethod($server, $options, $method) {
    $request = new Asar_Request($options);
    $request->setMethod($method);
    return $this->sendRequest($server, $request);
  }
  
  function GET($server, $options = array()) {
    return $this->sendRequestByMethod($server, $options, 'GET');
  }
  
  function POST($server, $options = array()) {
    return $this->sendRequestByMethod($server, $options, 'POST');
  }
  
  function PUT($server, $options = array()) {
    return $this->sendRequestByMethod($server, $options, 'PUT');
  }
  
  function DELETE($server, $options = array()) {
    return $this->sendRequestByMethod($server, $options, 'DELETE');
  }
}
