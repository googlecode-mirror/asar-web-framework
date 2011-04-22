<?php
namespace Asar\HttpServer;

use \Asar\Resource\ResourceInterface;
use \Asar\Request\RequestInterface;
use \Asar\Response;

/**
 * A wrapper class for making HTTP Requests
 */
class Fsocket implements ResourceInterface {
  
  private $host;
  
  /**
   * @param string $host the web address (e.g. www.google.com)
   */
  function __construct($host) {
    $this->host = $host;
  }
  
  /**
   * Converts the Request object to a raw HTTP request and returns a Response
   * object converted from the raw HTTP response sent by the host
   */
  function handleRequest(RequestInterface $request) {
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
  
  private function createRawHttpRequestString(RequestInterface $request) {
      $request->setHeader('Host', $this->getHostName());
      $request->setHeader('Connection', 'Close');
      return $request->export();
  }
  
  private function exportRawHttpResponse($raw) {
    $response = new Response;
    $response->import($raw);
    return $response;
  }
  
}
