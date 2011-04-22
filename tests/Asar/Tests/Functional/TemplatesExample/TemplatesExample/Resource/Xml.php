<?php

namespace Asar\Tests\Functional\TemplatesExample\TemplatesExample\Resource;

class Xml extends \Asar\Resource {
  
  public function GET() {
    return array('foo' => 'This is from Xml.php');
  }
  
}

