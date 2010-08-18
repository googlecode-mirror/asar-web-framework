<?php

class Asar_Representation
  implements Asar_Resource_Interface, Asar_Config_Interface
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
    $this->config_bag = new Asar_Config();
    $this->setUp();
    $this->config_bag->importConfig(new Asar_Config($this->config));
  }
  
  protected function setUp() {}
  
  function getConfig($key = null) {
    return $this->config_bag->getConfig($key);
  }
  
  function importConfig(Asar_Config_Interface $config) {
    if ($this->resource instanceof Asar_Config_Interface) {
      $this->resource->importConfig($config);
    }
    return $this->config_bag->importConfig($config);
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
