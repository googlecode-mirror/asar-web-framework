<?php

class Asar_MessageFilter_Development implements Asar_RequestFilter_Interface, Asar_ResponseFilter_Interface {
  
  private 
    $config,
    $debug,
    $exec_start,
    $printers = array(),
    $output_types = array(
      'text/plain' => 'txt'
    );
  
  function __construct(Asar_Config_Interface $config, Asar_Debug $debug) {
    $this->exec_start = microtime(true);
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
  
  private function getMemoryUsage() {
    $mem_usage = memory_get_usage(true);
    if ($mem_usage < 1024)
      return $mem_usage." bytes";
    elseif ($mem_usage < 1048576)
      return sprintf("%01.2f", round($mem_usage/1024,2))."KB";
    else
      return sprintf("%01.2f", round($mem_usage/1048576,2))."MB";
  }
  
  function filterResponse(Asar_Response_Interface $response) {
    $printer = $this->getPrinter(
      $this->getOutputType($response->getHeader('Content-Type'))
    );
    $this->debug->set(
      'Execution Time', 
      round(microtime(true) - $this->exec_start, 8) . ' microseconds'
    );
    $this->debug->set('Memory Used', $this->getMemoryUsage());
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
