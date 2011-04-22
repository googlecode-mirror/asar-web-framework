<?php

namespace Asar\Tests\Functional\StatusCodesExample\StatusCodesExample\Resource;

class Index extends \Asar\Resource {
  
  public function GET() {
    $this->setConfig('use_templates', false);
    return "This is a test.";
  }
}

