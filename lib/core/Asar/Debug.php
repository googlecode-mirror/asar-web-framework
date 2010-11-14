<?php

class Asar_Debug implements Iterator {
  
  private $data = array(), $valid = FALSE;
  
  function __construct() {
    
  }
  
  function set($key, $value) {
    $this->data[$key] = $value;
  }
  
  function get($key) {
    return $this->data[$key];
  }
  
  function rewind() {
    $this->valid = (FALSE !== reset($this->data));
  }
  
  function next(){
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
