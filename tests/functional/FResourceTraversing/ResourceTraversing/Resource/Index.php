<?php

class ResourceTraversing_Resource_Index extends Asar_Resource {
  
  function setUp() {
    $this->config['use_templates'] = false;
  }
  
  function GET() {
    return 'This is part of the resource traversing test.';
  }
    
}

