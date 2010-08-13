<?php

class Asar_Resource 
  implements Asar_Resource_Interface, Asar_Configurable_Interface,
  Asar_Named
{
  
  protected $config = array(
    'default_content_type' => 'text/html',
    'default_language'     => 'en',
    'use_templates'        => true
  );
  
  protected $request = null;
  
  function __construct() {
    $this->setUp();
  }
  
  protected function setUp() {}
  
  function getConfig($key) {
    if (array_key_exists($key, $this->config)) {
      return $this->config[$key];
    }
  }
  
  // TODO: Is this still needed?
  function setConfig($key, $value) {
    if (!isset($this->config[$key])) {
      $this->config[$key] = $value;
    }
  }
  
  function getName() {
    return get_class($this);
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $this->request = $request;
    $response = new Asar_Response(array(
      'headers' => array(
        'Content-Type' => $this->getConfig('default_content_type'),
        'Content-Language' => $this->getConfig('default_language')
      )
    ));
    
    try {
      $response->setContent(
        $this->runIfExists($request->getMethod())
      );
    } catch (Asar_Resource_Exception_MethodUndefined $e) {
      $response->setStatus(405);
    } catch (Exception $e) {
      $response->setStatus(500);
      $response->setContent($e->getMessage());
    }
    return $response;
  }
  
  private function runIfExists($method) {
    if (method_exists($this, $method)) {
      if ($method == 'POST') {
        $post_content = $this->request->getContent();
        $_POST = $post_content ? $post_content : array();
      }
      return $this->$method();
    }
    throw new Asar_Resource_Exception_MethodUndefined;
  }
  
}
