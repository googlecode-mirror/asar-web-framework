<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource\AParent;

class Child extends \Asar\Resource {
  
  function GET() {
    return '/a-parent/child GET.';
  }
    
}

