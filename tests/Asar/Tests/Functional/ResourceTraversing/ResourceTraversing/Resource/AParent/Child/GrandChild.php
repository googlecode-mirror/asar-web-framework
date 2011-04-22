<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource\AParent\Child;

class GrandChild extends \Asar\Resource {
  
  function GET() {
    return $this->getPath() . ' GET.';
  }
    
}

