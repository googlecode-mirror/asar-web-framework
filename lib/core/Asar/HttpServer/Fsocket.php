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
    $fp = @fsockopen($this->getHostName(), 80, $errno, $errstr, 30);
    if ($fp === false)
      throw new Asar_HttpServer_Exception(
        'Unable to connect to ' . $this->getHostName() . ':80.'
      );
    fwrite($fp, $request_str);
    $output = stream_get_contents($fp);
    fclose($fp);
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
    $R = new Asar_Response;
    $R->import($raw);
    return $R;
  }
  
}
