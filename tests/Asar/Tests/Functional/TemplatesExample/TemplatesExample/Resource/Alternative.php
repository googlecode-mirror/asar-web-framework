<?php

namespace Asar\Tests\Functional\TemplatesExample\TemplatesExample\Resource;

class Alternative extends \Asar\Resource {
  
  public function GET() {
    return array('p'  => 'This is an alternative template setup.');
  }
  
}

