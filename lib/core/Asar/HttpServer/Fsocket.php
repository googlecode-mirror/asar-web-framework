<?php

class Asar_HttpServer_Fsocket implements Asar_Resource_Interface {
  
  private $host;
  
  function __construct($host) {
    $this->host = $host;
  }
  
  function handleRequest(Asar_Request_Interface $request) {
    $rstr = $this->createRawHttpRequestString($request);
    if ($rstr) {
      return $this->exportRawHttpResponse(
        $this->sendRawHttpRequest($rstr)
      );
    }
  }
  
  private function sendRawHttpRequest($request_str) {
    $file_pointer = @fsockopen($this->getHostName(), 80, $errno, $errstr, 30);
    if ($file_pointer === false)
      throw new Asar_HttpServer_Exception(
        'Unable to connect to ' . $this->getHostName() . ':80.'
      );
    fwrite($file_pointer, $request_str);
    $output = stream_get_contents($file_pointer);
    fclose($file_pointer);
    return $output;
  }
  
  function getHostName() {
      return str_replace('http://', '', $this->host);
  }
  
  private function createRawHttpRequestString(Asar_Request_Interface $request) {
      $request->setHeader('Host', $this->getHostName());
      $request->setHeader('Connection', 'Close');
      return $request->export();
  }
  
  private function exportRawHttpResponse($raw) {
    $response = new Asar_Response;
    $response->import($raw);
    return $response;
  }
  
}
