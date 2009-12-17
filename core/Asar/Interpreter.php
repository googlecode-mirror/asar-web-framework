<?php
class Asar_Interpreter implements Asar_Interprets {
  
  private $response;  
    
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
    //var_dump($response);
    //var_dump(headers_list());
  }
  
  function exportResponseHeaders($response) {
    $headers = $response->getHeaders();
    $this->response = $response;
    $i = 0;
    $length = count($headers);
    foreach ($headers as $name => $value) {
      $i++;
      $this->header($name . ': ' . $value);
    }
    $messages = array(
      200 => 'OK',
      404 => 'Not Found'
    );
    $this->header(
      'HTTP/1.1 ' . $response->getStatus() . ' '. 
      $messages[$response->getStatus()]
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
