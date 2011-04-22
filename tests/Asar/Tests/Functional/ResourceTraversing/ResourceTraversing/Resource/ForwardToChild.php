<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource;

class ForwardToChild extends \Asar\Resource {
  
  function GET() {
    $this->forwardTo('AParent\Child');
  }
    
}

