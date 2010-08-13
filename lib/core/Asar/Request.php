<?php

class Asar_Request extends Asar_Message implements Asar_Request_Interface {
  
  private 
    $path    = '/', 
    $method  = 'GET', 
    $params  = array();
  protected
    $headers = array('Accept' => 'text/html'), 
    $content;
    
  function __construct($options = array()) {
    parent::__construct($options);
    $this->setIfExists('path', $options, 'setPath');
    $this->setIfExists('method', $options, 'setMethod');
    $this->setIfExists('params', $options, 'setParams');
  }

  function setPath($path) {
    $this->path = $path;
  }
  
  function getPath() {
    return $this->path;
  }
  
  function setMethod($method) {
    $this->method = $method;
  }
  
  function getMethod() {
    return $this->method;
  }
  
  function setParams(array $params) {
    $this->params = $params;
  }
  
  function getParams() {
    return $this->params;
  }
  
  function setContent($content) {
    $this->content = $content;
  }
  
  function getContent() {
    return $this->content;
  }
  
  function export() {
    $str = sprintf("%s %s HTTP/1.1\r\n", 
      $this->getMethod(), $this->getPath()
    );
    $headers = $this->getHeaders();
    $msg_body = '';
    if ($this->getMethod() == 'POST') {
      $headers['Content-Type'] = 'application/x-www-form-urlencoded';
      $msg_body = $this->createParamStr($this->getContent());
      $headers['Content-Length'] = strlen($msg_body);
    }
    foreach ($headers as $key => $value) {
      $str .= $key . ': ' . $value . "\r\n";
    }
    return $str . "\r\n" . $msg_body;
  }
  
  private function createParamStr($params) {
    if (!is_array($params))
      return '';
    $post_pairs = array();
    foreach($params as $key => $value) {
      $post_pairs[] = rawurlencode($key) . '=' . rawurlencode($value);
    }
    return implode('&', $post_pairs);
  }

}
