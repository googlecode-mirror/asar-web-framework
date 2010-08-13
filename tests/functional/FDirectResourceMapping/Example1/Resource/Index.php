<?php

class Example1_Resource_Index extends Asar_Resource {
  
  function setUp() {
    $this->config['use_templates'] = false;
  }
  
  function GET() {
    return 'Hello World!';
  }
    
}

