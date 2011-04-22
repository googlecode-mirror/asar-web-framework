<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource;

class RedirectOne extends \Asar\Resource {
  
  function GET() {
    $this->redirectTo('AParent\Child\GrandChild');
  }
    
}
