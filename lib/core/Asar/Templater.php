<?php
// TODO: This object seems to have too much responsibility. Refactor.
class Asar_Templater
  implements Asar_Resource_Interface, Asar_Config_Interface {
  
  private $resource, $renderer;
  
  function __construct(
    Asar_Resource_Interface $resource,
    Asar_TemplateRenderer $renderer
  ) {
    $this->resource = $resource;
    $this->renderer = $renderer;
  }
  
  //TODO: What to do when handleRequest returns void/null
  function handleRequest(Asar_Request_Interface $request) {
    $response = $this->resource->handleRequest($request);
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
    return $this->resource->getConfig($key);
  }
  
  function importConfig(Asar_Config_Interface $config) {
    
  }
  
}
