<?php

namespace Asar\Tests\Functional\TemplatesExample\TemplatesExample\Resource;

class SetLayout extends \Asar\Resource {
  
  public function GET() {
    return array(
      'p'  => 'This is the paragraph from SetLayout.php'
    );
  }
  
}

