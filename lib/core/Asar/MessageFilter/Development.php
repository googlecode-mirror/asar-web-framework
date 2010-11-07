<?php

class Asar_MessageFilter_Development implements Asar_MessageFilter_Interface {
  
  private $config;
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  function filterRequest(Asar_Request_Interface $request) {
    return $request;
  }
  
  function filterResponse(Asar_Response_Interface $response) {
    return $response;
  }
  
}