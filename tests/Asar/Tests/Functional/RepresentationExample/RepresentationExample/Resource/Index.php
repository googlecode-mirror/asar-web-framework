<?php

namespace Asar\Tests\Functional\RepresentationExample\RepresentationExample\Resource;

class Index extends \Asar\Resource {
  
  function GET() {
    return array(
      'h1' => 'Hello World!',
      'p'  => 'This is the paragraph. Easy, no?'
    );
  }
}
