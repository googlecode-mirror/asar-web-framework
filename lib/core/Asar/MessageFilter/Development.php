<?php

class Asar_MessageFilter_Development implements Asar_RequestFilter_Interface, Asar_ResponseFilter_Interface {
  
  private 
    $config,
    $debug,
    $printers = array(),
    $output_types = array(
      'text/plain' => 'txt'
    );
  
  function __construct(Asar_Config_Interface $config, Asar_Debug $debug) {
    $this->config = $config;
    $this->debug  = $debug;
  }
  
  function setPrinter($type, Asar_DebugPrinter_Interface $printer) {
    $this->printers[$type] = $printer;
  }
  
  function filterRequest(Asar_Request_Interface $request) {
    $request->setHeader('Asar-Internal-Debug', $this->debug);
    return $request;
  }
  
  function filterResponse(Asar_Response_Interface $response) {
    $printer = $this->getPrinter(
      $this->getOutputType($response->getHeader('Content-Type'))
    );
    $response->setContent(
      $printer->printDebug(
        $this->debug, $response->getContent()
      )
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