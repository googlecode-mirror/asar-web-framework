<?php

class ResourceTraversing_Resource_Blog extends Asar_Resource {
  
  
  
  function setUp() {
    $this->config['use_templates'] = false;
  }
  
  function GET() {
    return 'This is part the blog list.';
  }
    
}

