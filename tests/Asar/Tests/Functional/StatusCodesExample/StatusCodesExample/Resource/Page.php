<?php

namespace Asar\Tests\Functional\StatusCodesExample\StatusCodesExample\Resource;

class Page extends \Asar\Resource {
  function GET() {
    return array('heading' => "This is a test.");
  }
}

