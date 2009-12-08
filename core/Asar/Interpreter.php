<?php
class Asar_Interpreter implements Asar_Interprets {
  
  function interpretFor(Asar_Requestable $requestable) {
    //TODO: How will this behave if $response is not an Asar_Response
    $response = $requestable->handleRequest($this->createRequest());
    $this->exportResponse($response);
  }
  
  function createRequest() {
    $request = new Asar_Request;
    if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
      $request->setMethod($_SERVER['REQUEST_METHOD']);
    }
    if (array_key_exists('REQUEST_URI', $_SERVER)) {
      $request->setPath($this->createPathFromUri($_SERVER['REQUEST_URI']));
    }
    foreach ($_SERVER as $key => $value) {
      if (strpos($key, 'HTTP_') === 0) {
        $request->setHeader(str_replace('HTTP_', '', $key), $value);
      }
    }
    if (array_key_exists('REQUEST_METHOD', $_SERVER) && $_SERVER['REQUEST_METHOD'] === 'POST') {
      $request->setContent($_POST);
    }
    
    return $request;
  }
  
  function exportResponse(Asar_Response $response) {
    $this->exportResponseHeaders($response);
    echo $response->getContent();
  }
  
  function exportResponseHeaders($response) {
    $headers = $response->getHeaders();
    foreach ($headers as $name => $value) {
      $this->header($name . ': ' . $value);
    }
  }
  
  function header($header) {
    header($header);
  }
  
  private static function createPathFromUri($uri) {
    $qrstr_start = strpos($uri, '?');
    if ($qrstr_start > 0) {
      return substr($uri, 0, strpos($uri, '?'));
    }
    return $uri;
  }
}