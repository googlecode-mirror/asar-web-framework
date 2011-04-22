<?php

namespace Asar\Tests\Functional\DebuggingExample\DebuggingExample\Resource;

class Index extends \Asar\Resource {
  function GET() {
    return array(
      'h1' => 'Debugging Tests'
    );
  }
}
