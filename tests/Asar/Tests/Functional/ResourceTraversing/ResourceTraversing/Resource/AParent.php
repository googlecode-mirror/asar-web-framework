<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource;

class AParent extends \Asar\Resource {
  
  function GET() {
    return '/a-parent GET.';
  }
    
}

