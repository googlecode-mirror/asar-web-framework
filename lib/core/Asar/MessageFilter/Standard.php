<?php

class Asar_MessageFilter_Standard implements Asar_MessageFilter_Interface {
  
  private $config;
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  function filterRequest(Asar_Request_Interface $request) {
    return $request;
  }
  
  function filterResponse(Asar_Response_Interface $response) {
    if ($response->getStatus() === 302) {
      $response->setHeader(
        'Location', 
        $this->config->getConfig('site_protocol') . '://' . $this->config->getConfig('site_domain') . $response->getHeader('Location')
      );
    }
    return $response;
  }
  
}