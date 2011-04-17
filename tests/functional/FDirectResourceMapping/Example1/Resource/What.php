<?php

class Example1_Resource_What extends \Asar\Resource {
  
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

