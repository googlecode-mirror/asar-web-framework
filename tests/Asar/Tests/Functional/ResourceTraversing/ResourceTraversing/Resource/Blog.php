<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource;

class Blog extends \Asar\Resource {
  
  function GET() {
    return '/blog GET.';
  }
    
}

