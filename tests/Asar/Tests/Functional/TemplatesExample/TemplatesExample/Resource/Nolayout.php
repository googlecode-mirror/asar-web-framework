<?php

namespace Asar\Tests\Functional\TemplatesExample\TemplatesExample\Resource;

class Nolayout extends \Asar\Resource {
  public function GET() {
    return array(
      'h1' => 'This is the main heading.',
      'p'  => 'This is the paragraph. Easy, no?'
    );
  }
}

