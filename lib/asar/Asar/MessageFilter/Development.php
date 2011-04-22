<?php
namespace Asar\MessageFilter;

use \Asar\RequestFilter\RequestFilterInterface;
use \Asar\ResponseFilter\ResponseFilterInterface;
use \Asar\Request\RequestInterface;
use \Asar\Response\ResponseInterface;
use \Asar\Config\ConfigInterface;
use \Asar\Debug;
use \Asar\DebugPrinter\DebugPrinterInterface;
use \Asar\DebugPrinter\NullDebugPrinter;

/**
 */
class Development
  implements RequestFilterInterface, ResponseFilterInterface
{
  
  private 
    $config,
    $debug,
    $exec_start,
    $printers = array(),
    $output_types = array(
      'text/plain' => 'txt'
    );
  
  function __construct(ConfigInterface $config, Debug $debug) {
    $this->exec_start = microtime(true);
    $this->config = $config;
    $this->debug  = $debug;
  }
  
  function setPrinter($type, DebugPrinterInterface $printer) {
    $this->printers[$type] = $printer;
  }
  
  function filterRequest(RequestInterface $request) {
    $request->setHeader('Asar-Internal-Debug', $this->debug);
    $app_name = $request->getHeader('Asar-Internal-Application-Name');
    if ($app_name) {
      $this->debug->set('Application', $app_name);
    }
    return $request;
  }
  
  private function getMemoryUsage() {
    $mem_usage = memory_get_usage(true);
    if ($mem_usage < 1024)
      return $mem_usage." bytes";
    elseif ($mem_usage < 1048576)
      return sprintf("%01.2f", round($mem_usage/1024, 2))."KB";
    else
      return sprintf("%01.2f", round($mem_usage/1048576, 2))."MB";
  }
  
  function filterResponse(ResponseInterface $response) {
    $printer = $this->getPrinter(
      $this->getOutputType($response->getHeader('Content-Type'))
    );
    $this->addDebugExecutionTime();
    $this->addDebugMemoryUsage();
    $response->setContent(
      $printer->printDebug(
        $this->debug, $response->getContent()
      )
    );
    return $response;
  }
  
  private function addDebugExecutionTime() {
    $this->debug->set(
      'Execution Time', 
      round(microtime(true) - $this->exec_start, 8) . ' microseconds'
    );
  }
  
  private function addDebugMemoryUsage() {
    $this->debug->set('Memory Used', $this->getMemoryUsage());
  }
  
  private function getPrinter($output_type) {
     if (isset($this->printers[$output_type])) {
       return $this->printers[$output_type];
     }
     return new NullDebugPrinter;
  }
  
  function getOutputType($content_type) {
    if (isset($this->output_types[$content_type])) {
      return $this->output_types[$content_type];
    }
    return 'html';
  }
  
}
