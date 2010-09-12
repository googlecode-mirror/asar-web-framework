<?php

class Asar_Resource 
  implements Asar_Resource_Interface, Asar_Config_Interface
{
  
  protected $config_bag = null;
  
  protected $config = array();
  
  protected $request = null;
  
  function __construct() {
    $this->config_bag = new Asar_Config();
    $this->setUp();
    $this->config_bag->importConfig(new Asar_Config($this->config));
  }
  
  protected function setUp() {}
  
  function getConfig($key = null) {
    return $this->config_bag->getConfig($key);
  }
  
  protected function setConfig($key, $value) {
    $new_config_bag = new Asar_Config(array($key => $value));
    $new_config_bag->importConfig($this->config_bag);
    $this->config_bag = $new_config_bag;
  }
  
  function importConfig(Asar_Config_Interface $config) {
    return $this->config_bag->importConfig($config);
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
      if (!$this->qualify($this->getPathComponents())) {
        throw new Asar_Resource_Exception_NotFound;
      }
      $response->setContent(
        $this->runIfExists($request->getMethod())
      );
    } catch (Asar_Resource_Exception_NotFound $e) {
      $response->setStatus(404);
    } catch (Asar_Resource_Exception_MethodUndefined $e) {
      $response->setStatus(405);
    } catch (Asar_Resource_Exception_ForwardRequest $e) {
      throw $e;
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
  
  function forwardTo($resource_name) {
    $e = new Asar_Resource_Exception_ForwardRequest($resource_name);
    $e->setPayload(array('request' => $this->request));
    throw $e;
  }
  
  function qualify($path) {
    return TRUE;
  }
  
  private function getResourceNameAsArray() {
    $cname = get_class($this);
    return explode('_', substr($cname, strpos($cname, '_Resource_') + 10));
  }
  
  function getPath() {
    return $this->request->getPath();
  }
  
  function getPermaPath() {
    $relevant = $this->getResourceNameAsArray();
    return '/' . implode('/', array_map(
      array('Asar_Utility_String', 'dashLowerCase'), $relevant
    ));
  }
  
  protected function getPathComponents() {
    $relevant = $this->getResourceNameAsArray();
    $keys = array_map(
      array('Asar_Utility_String', 'dashLowerCase'), $relevant
    );
    $path = explode('/', $this->getPath());
    array_shift($path);
    for (
      $i = 0, $components = array(), $size = count($keys); $i < $size; $i++
    ) {
      if ($this->strStartsWith($keys[$i], 'rt-')) {
        $components[substr($keys[$i], 3)] = $path[$i];
      } else {
        $components[$keys[$i]] = $path[$i];
      }
    }
    return $components;
  }
  
  private function strStartsWith($str, $prefix) {
    return strpos($str, $prefix) === 0;
  }
  
}
