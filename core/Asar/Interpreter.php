<?php
class Asar_Interpreter implements Asar_Interprets {
  
  private $response;  
    
  function interpretFor(Asar_Requestable $requestable) {
    //TODO: How will this behave if $response is not an Asar_Response
    $response = $requestable->handleRequest($this->importRequest());
    $this->exportResponse($response);
  }
  
  function importRequest() {
    $request = new Asar_Request;
    if ($this->isInServerVar('REQUEST_METHOD')) {
      $request->setMethod($_SERVER['REQUEST_METHOD']);
    }
    if ($this->isInServerVar('REQUEST_URI')) {
      $request->setPath($this->createPathFromUri($_SERVER['REQUEST_URI']));
    }
    $this->importHeadersToRequest($request);
    if ($this->isPostRequest()) {
      $request->setContent($_POST);
    }
    return $request;
  }
  
  private function importHeadersToRequest($request) {
    foreach ($_SERVER as $key => $value) {
      if (strpos($key, 'HTTP_') === 0) {
        $request->setHeader(str_replace('HTTP_', '', $key), $value);
      }
    }
  }
  
  private function isInServerVar($key) {
    return array_key_exists($key, $_SERVER);
  }
  
  private function isPostRequest() {
    return $this->isInServerVar('REQUEST_METHOD') &&
      $_SERVER['REQUEST_METHOD'] === 'POST';
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
    $status = $response->getStatus();
    $this->header(
      "HTTP/1.1 $status " . Asar_Response::getStatusMessage($status)
    );
  }
  
  function header() {
    $args = func_get_args();
    @call_user_func_array('header', $args);
  }
  
  private static function createPathFromUri($uri) {
    $qrstr_start = strpos($uri, '?');
    if ($qrstr_start > 0) {
      return substr($uri, 0, strpos($uri, '?'));
    }
    return $uri;
  }
}
