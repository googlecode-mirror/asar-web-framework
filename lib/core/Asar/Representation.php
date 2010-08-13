<?php

class Asar_Representation
  implements Asar_Resource_Interface, Asar_Configurable_Interface
{
  
  protected
    $resource,
    $config = array(),
    $methods = array(
      'text/html'        => 'Html',
      'text/plain'       => 'Txt',
      'application/xml'  => 'Xml',
      'application/json' => 'Json',
    );
  
  function __construct(Asar_Resource_Interface $resource) {
    $this->resource = $resource;
    $this->setUp();
  }
  
  protected function setUp() {}
  
  function setConfig($key, $value) {
    if (!isset($this->config[$key])) {
      $this->config[$key] = $value;
    }
  }
  
  function getConfig($key) {
    return $this->config[$key];
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $response = $this->resource->handleRequest($request);
    $response->setContent(
      $this->callRequestMethod(
        $request->getMethod(), $request->getHeader('Accept'), $response
      )
    );
    return $response;
  }
  
  private function callRequestMethod($method, $type, $response) {
    if (
      isset($this->methods[$type]) && 
      method_exists($this, $method . $this->methods[$type])
    ) {
      return call_user_func_array(
        array($this, $method . $this->methods[$type]),
        array($response->getContent())
      );
    } else {
      $response->setStatus(406);
    }
  }
}
