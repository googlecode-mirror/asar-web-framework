<?php

namespace Asar\Tests\Functional\TemplatesExample\TemplatesExample\Resource;

class ContentNegotiation extends \Asar\Resource {
  
  public function GET() {
    return array('foo' => 'This is from ContentNegotiation.php');
  }
  
}
