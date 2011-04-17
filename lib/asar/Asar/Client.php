<?php
namespace Asar;

use \Asar\Resource\ResourceInterface;
use \Asar\Client\Exception\UnknownServerType;
use \Asar\Request;

/**
 * @package Asar
 * @subpackage core
 */
class Client {

  function sendRequest($server, $request) {
    if ($server instanceof ResourceInterface) {
      return $server->handleRequest($request);
    }
    throw new UnknownServerType;
  }
  
  private function sendRequestByMethod($server, $options, $method) {
    $request = new Request($options);
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
