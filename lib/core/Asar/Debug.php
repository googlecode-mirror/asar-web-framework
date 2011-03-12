<?php

class Asar_Debug implements Iterator {
  
  private $data = array(), $valid = FALSE;
  
  function set($key, $value) {
    $this->data[$key] = $value;
  }
  
  function get($key) {
    if (isset($this->data[$key])) {
      return $this->data[$key];
    }
    return null;
  }
  
  function rewind() {
    $this->valid = (FALSE !== reset($this->data));
  }
  
  function next() {
    $this->valid = (FALSE !== next($this->data)); 
  }
  
  function valid() {
    return $this->valid;
  }
  
  function current() {
    return current($this->data);
  }
  
  function key() {
    return key($this->data);
  }
  
}
