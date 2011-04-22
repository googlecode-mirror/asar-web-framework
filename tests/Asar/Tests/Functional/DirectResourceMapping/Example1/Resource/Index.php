<?php
namespace Asar\Tests\Functional\DirectResourceMapping\Example1\Resource;

class Index extends \Asar\Resource {
  
  function setUp() {
    $this->config['use_templates'] = false;
  }
  
  function GET() {
    return 'Hello World!';
  }
    
}

