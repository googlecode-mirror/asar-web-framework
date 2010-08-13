<?php

class Asar_Message implements Asar_Message_Interface {
  
  protected $headers = array(), $content = '';
  
  function __construct($options = array()) {
    $this->setIfExists('content', $options, 'setContent');
    $this->setIfExists('headers', $options, 'setHeaders');
  }
  
  protected function setIfExists($key, $options, $method) {
    if (array_key_exists($key, $options)) {
      call_user_func(array($this, $method), $options[$key]);
    }
  }
  
  function setHeader($name, $value) {
    $this->headers[$this->dashCamelCase($name)] = $value;
  }
  
  function getHeader($name) {
    $camel_key = $this->dashCamelCase($name);
    if (isset($this->headers[$camel_key])) {
      return $this->headers[$camel_key];
    }
  }
  
  function setHeaders(array $headers) {
    foreach ($headers as $name => $value) {
      $this->headers[$this->dashCamelCase($name)] = $value;
    }
  }
  
  function getHeaders() {
    return $this->headers;
  }
  
  function setContent($content) {
    $this->content = $content;
  }
  
  function getContent() {
    return $this->content;
  }
  
  private function dashCamelCase($string) {
    return str_replace(' ', '-', $this->ucwordsLower($string));
  }

  private function ucwordsLower($string) {
    return ucwords(
      strtolower(str_replace(array('-', '_'), ' ', $string))
    );
  }
}