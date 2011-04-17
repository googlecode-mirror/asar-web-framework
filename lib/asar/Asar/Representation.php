<?php
namespace Asar;
use \Asar\Resource\ResourceInterface;
use \Asar\Config\ConfigInterface;
use \Asar\Request\RequestInterface;
/**
 * @package Asar
 * @subpackage core
 */
class Representation implements ResourceInterface, ConfigInterface
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
  
  function __construct(ResourceInterface $resource) {
    $this->resource = $resource;
    $this->config_bag = new Config();
    $this->setUp();
    $this->config_bag->importConfig(new Config($this->config));
  }
  
  protected function setUp() {}
  
  function getConfig($key = null) {
    return $this->config_bag->getConfig($key);
  }
  
  function importConfig(ConfigInterface $config) {
    if ($this->resource instanceof Config\ConfigInterface) {
      $this->resource->importConfig($config);
    }
    return $this->config_bag->importConfig($config);
  }
  
  function handleRequest(RequestInterface $request) {
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
