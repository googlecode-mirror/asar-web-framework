<?php
class Asar_Response implements Asar_Response_Interface
{
  private $content = '';
  private $status;
  private $headers = array();
  private static $reason_phrases = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable'
  );
  
  function setContent($content) {
    $this->content = $content;
  }  
  
  function getContent() {
    return $this->content;
  }
  
  function setStatus($status) {
    $this->status = $status;
  }
  
  function getStatus() {
    return $this->status;
  }
  
  static function getStatusMessage($status) {
    if (!array_key_exists($status, self::$reason_phrases)) {
      return null;
    }
    return self::$reason_phrases[$status];
  }
  
  function setHeader($key, $value) {
    $this->headers[$key] = $value;
  }
  
  function getHeader($key) {
    if (array_key_exists($key, $this->headers)) {
      return $this->headers[$key];
    }
    return null;
  }
  
  function setHeaders(array $headers){
    foreach ($headers as $name => $value) {
      $this->setHeader($name, $value);
    }
  }
  
  function getHeaders(){
    return $this->headers;
  }
}

