<?php
class Asar_Templater
  implements Asar_Resource_Interface, Asar_Config_Interface,
    Asar_PathDiscover_Interface
{
  
  private $resource, $renderer, $config;
  
  function __construct(
    Asar_Resource_Interface $resource,
    Asar_TemplateRenderer $renderer
  ) {
    $this->resource = $resource;
    if ($this->resource instanceof Asar_Config_Interface) {
      $this->config = $resource;
    } else {
      $this->config = new Asar_Config;
    }
    $this->renderer = $renderer;
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $response = $this->resource->handleRequest($request);
    if (!$response instanceof Asar_Response_Interface) {
      throw new Asar_Templater_Exception(
        "Unable to create template. The Resource did not return a response object."
      );
    }
    if ($this->responseTemplatable($response)) {
      $response = $this->renderer->renderFor(
        get_class($this->resource), $response, $request
      );
    }
    return $response;
  }
  
  private function responseTemplatable($response) {
    return $this->resource->getConfig('use_templates') && 
    $response->getStatus() == 200;
  }
  
  function getConfig($key = null) {
    return $this->config->getConfig($key);
  }
  
  function importConfig(Asar_Config_Interface $config) {
    return $this->config->importConfig($config);
  }
  
  function getPermaPath($path_params = array()) {
    return $this->resource->getPermaPath($path_params);
  }
  
}
