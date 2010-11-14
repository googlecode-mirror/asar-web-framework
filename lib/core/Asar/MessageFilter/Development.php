<?php

class Asar_MessageFilter_Development implements Asar_MessageFilter_Interface {
  
  private 
    $config,
    $printers = array(),
    $output_types = array(
      'text/plain' => 'txt'
    );
  
  function __construct(Asar_Config_Interface $config) {
    $this->config = $config;
  }
  
  function setPrinter($type, Asar_DebugPrinter_Interface $printer) {
    $this->printers[$type] = $printer;
  }
  
  function filterRequest(Asar_Request_Interface $request) {
    $a = $request->getHeader('Asar-Internal');
    if (!is_array($a)) {
      $a = array();
    }
    $a['debug'] = new Asar_Debug;
    $request->setHeader('Asar-Internal', $a);
    return $request;
  }
  
  function filterResponse(Asar_Response_Interface $response) {
    $printer = $this->getPrinter(
      $this->getOutputType($response->getHeader('Content-Type'))
    );
    $internal_headers = $response->getHeader('Asar-Internal');
    $response->setContent(
      $printer->printDebug($internal_headers['debug'], $response->getContent())
    );
    return $response;
  }
  
  private function getPrinter($output_type) {
     if (isset($this->printers[$output_type])) {
       return $this->printers[$output_type];
     }
     return new Asar_DebugPrinter_Null;
  }
  
  function getOutputType($content_type) {
    if (isset($this->output_types[$content_type])) {
      return $this->output_types[$content_type];
    }
    return 'html';
  }
  
}