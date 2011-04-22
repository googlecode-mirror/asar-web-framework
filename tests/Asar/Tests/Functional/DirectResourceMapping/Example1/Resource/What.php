<?php
namespace Asar\Tests\Functional\DirectResourceMapping\Example1\Resource;

class What extends \Asar\Resource {
  
  function setUp() {
    $this->config['use_templates'] = false;
  }

  function GET() {
    return "What's your name?";
  }
  
  function POST() {
    $name = $_POST['name'];
    return "Hello $name!";
  }
}

