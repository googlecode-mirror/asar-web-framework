<?php

namespace Asar\Tests\Functional\ResourceTraversing\ResourceTraversing\Resource\Blog\RtYear;

class RtMonth extends \Asar\Resource {
  
  function GET() {
    return $this->request->getPath() . ' GET.';
  }
  
  function qualify($path) {
    return 
      preg_match('/^[1-9][0-9]{3}$/' , $path['year']) &&
      preg_match('/^[0-1][0-9]$/' , $path['month']);
  }
}
