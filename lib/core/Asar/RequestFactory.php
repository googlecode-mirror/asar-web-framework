<?php

class Asar_RequestFactory {
  
  function createRequest($server = array(), $params = array(), $post = null) {
    $options = array();
    $options['method'] = $this->getIfExists('REQUEST_METHOD', $server);
    $options['path'] = $this->getIfExists('REQUEST_URI', $server);
    $options['params'] = $params;
    if ($options['method'] === 'POST') {
      $options['content'] = $post;
    }
    $options['headers'] = $this->createHeaders($server);
    return new Asar_Request($options);
  }
  
  private function getIfExists($key, $array, $default = null) {
    return array_key_exists($key, $array) ? $array[$key] : null;
  }
  
  private function createHeaders($server) {
    $headers = array();
    foreach ($server as $key => $value) {
      if (strpos($key, 'HTTP_') === 0) {
        $headers[str_replace('HTTP_', '', $key)] = $value;
      }
    }
    return $headers;
  }
  
}
