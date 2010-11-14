<?php

class Asar_MessageFilter_Standard implements Asar_RequestFilter_Interface, Asar_ResponseFilter_Interface {
  
  private $config;
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  function filterRequest(Asar_Request_Interface $request) {
    echo "\nStandard::filterRequest";
    $this->removeInternalHeaders($request);
    return $request;
  }
  
  function filterResponse(Asar_Response_Interface $response) {
    echo "\nStandard::filterResponse";
    $this->reformatLocationHeader($response);
    $this->removeInternalHeaders($response);
    return $response;
  }
  
  private function reformatLocationHeader(Asar_Response $response) {
    $location = $response->getHeader('Location');
    if ($location && !preg_match('/^http[s]?:\/\//', $location)) {
      $response->setHeader(
        'Location', 
        $this->config->getConfig('site_protocol') . '://' . 
          $this->config->getConfig('site_domain') . $location
      );
    }
  }
  
  private function removeInternalHeaders(Asar_Message $message) {
    if ($message->getHeader('Asar-Internal')) {
      $message->unsetHeader('Asar-Internal');
    }
  }
  
}