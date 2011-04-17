<?php

class Example1_Resource_Index extends \Asar\Resource {
  
  function setUp() {
    $this->config['use_templates'] = false;
  }
  
  function GET() {
    return 'Hello World!';
  }
    
}

